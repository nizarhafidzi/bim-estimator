<?php

namespace App\Livewire;

use App\Models\CostResult;
use App\Models\Project;
use App\Models\AhspMaster;
use Livewire\Component;

class ProjectReport extends Component
{
    public $project;
    public $groupedBoq = [];
    public $recapBoq = [];
    public $totalCost = 0;
    public $currentDate;

    public function mount($id)
    {
        $this->project = Project::findOrFail($id);
        $this->currentDate = now()->format('d F Y');

        // Jika belum ada library, stop
        if (!$this->project->cost_library_id) return;

        // 1. Ambil Data Referensi
        $ahspLookup = AhspMaster::where('cost_library_id', $this->project->cost_library_id)
                        ->get()
                        ->keyBy('code');

        // 2. Ambil Hasil Hitungan (Matched Only)
        $results = CostResult::with('element')
                    ->where('project_id', $id)
                    ->where('status', 'matched')
                    ->get();

        $this->totalCost = $results->sum('total_cost');

        // 3. LOGIC GROUPING (Sama dengan Calculator)
        // Group by Division -> Work Code
        $grouped = $results->groupBy(function($item) use ($ahspLookup) {
            $code = $item->matched_work_code;
            return $ahspLookup[$code]->division ?? 'GENERAL WORK';
        });

        // Siapkan Data Detail
        $this->groupedBoq = $grouped->map(function($itemsInDivision) use ($ahspLookup) {
            return $itemsInDivision->groupBy('matched_work_code')->map(function($itemsInCode) use ($ahspLookup) {
                $code = $itemsInCode->first()->matched_work_code;
                $ahsp = $ahspLookup[$code] ?? null;
                return [
                    'code' => $code,
                    'name' => $ahsp ? $ahsp->name : 'Unknown',
                    'unit' => $ahsp ? $ahsp->unit : '-',
                    'volume' => $itemsInCode->sum('element.volume'),
                    'unit_price' => $itemsInCode->first()->unit_price_applied,
                    'total' => $itemsInCode->sum('total_cost'),
                ];
            })->sortBy('code');
        })->sortKeys();

        // Siapkan Data Rekap (Untuk Sheet Depan)
        $this->recapBoq = $grouped->map(function($items) {
            return $items->sum('total_cost');
        })->sortKeys();
    }

    public function render()
    {
        return view('livewire.project-report')->layout('layouts.app');
    }
}