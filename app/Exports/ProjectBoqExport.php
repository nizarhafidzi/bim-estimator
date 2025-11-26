<?php

namespace App\Exports;

use App\Models\CostResult;
use App\Models\Project;
use App\Models\AhspMaster;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

class ProjectBoqExport implements WithMultipleSheets
{
    protected $projectId;

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    public function sheets(): array
    {
        return [
            new BoqRecapSheet($this->projectId), // Sheet 1: Rekap Per Divisi
            new BoqDetailSheet($this->projectId), // Sheet 2: Detail Per Pekerjaan
        ];
    }
}

// --- SHEET 1: REKAPITULASI (PER DIVISI) ---
class BoqRecapSheet implements FromView, WithTitle
{
    protected $projectId;
    public function __construct($id) { $this->projectId = $id; }

    public function view(): View
    {
        $project = Project::find($this->projectId);
        
        $recap = CostResult::query()
            // --- PERBAIKAN DI SINI JUGA ---
            ->where('cost_results.project_id', $this->projectId)
            ->where('cost_results.status', 'matched')
            // ------------------------------
            ->join('ahsp_masters', 'cost_results.matched_work_code', '=', 'ahsp_masters.code')
            ->where('ahsp_masters.cost_library_id', $project->cost_library_id)
            ->selectRaw('
                ahsp_masters.division, 
                SUM(cost_results.total_cost) as total_cost
            ')
            ->groupBy('ahsp_masters.division')
            ->get();

        return view('exports.boq-recap', [
            'project' => $project,
            'recap' => $recap
        ]);
    }

    public function title(): string { return 'REKAPITULASI'; }
}

// --- SHEET 2: DETAIL ITEM PEKERJAAN ---
class BoqDetailSheet implements FromView, WithTitle
{
    protected $projectId;
    public function __construct($id) { $this->projectId = $id; }

    public function view(): View
    {
        $project = Project::find($this->projectId);

        // Ambil Detail Item
        $details = CostResult::query() // Gunakan query() agar lebih eksplisit
            // --- PERBAIKAN DI SINI: Tambahkan 'cost_results.' ---
            ->where('cost_results.project_id', $this->projectId) 
            ->where('cost_results.status', 'matched')
            // ----------------------------------------------------
            
            ->join('ahsp_masters', 'cost_results.matched_work_code', '=', 'ahsp_masters.code')
            ->where('ahsp_masters.cost_library_id', $project->cost_library_id)
            ->join('model_elements', 'cost_results.model_element_id', '=', 'model_elements.id')
            ->selectRaw('
                ahsp_masters.division,
                ahsp_masters.sub_division,
                ahsp_masters.code,
                ahsp_masters.name,
                ahsp_masters.unit,
                cost_results.unit_price_applied as price,
                SUM(model_elements.volume) as volume,
                SUM(cost_results.total_cost) as total
            ')
            ->groupBy('ahsp_masters.division', 'ahsp_masters.sub_division', 'ahsp_masters.code', 'ahsp_masters.name', 'ahsp_masters.unit', 'cost_results.unit_price_applied')
            ->orderBy('ahsp_masters.division')
            ->orderBy('ahsp_masters.code')
            ->get()
            ->groupBy('division');

        return view('exports.boq-detail', [
            'project' => $project,
            'details' => $details
        ]);
    }

    public function title(): string { return 'BILL OF QUANTITIES'; }
}