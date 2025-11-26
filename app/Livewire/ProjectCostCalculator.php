<?php

namespace App\Livewire;

use App\Models\CostLibrary;
use App\Models\CostResult;
use App\Models\Project;
use App\Services\CostEstimationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Exports\ProjectBoqExport;
use Maatwebsite\Excel\Facades\Excel; 

class ProjectCostCalculator extends Component
{
    // State
    public $projectId;
    public $selectedLibraryId; // ID Buku Harga yang dipilih
    
    // Data Tampilan
    public $summary = null;
    public $groupedResults = []; // Hasil BOQ yang sudah dikelompokkan
    public $unassignedCount = 0;

    // 1. Saat Project Dipilih, Load Data
    public function updatedProjectId()
    {
        $project = Project::find($this->projectId);
        if ($project) {
            // Otomatis isi dropdown library jika project sudah punya settingan sebelumnya
            $this->selectedLibraryId = $project->cost_library_id;
            $this->loadResults();
        }
    }

    // 2. Saat Library Diganti, Simpan ke Project (Tapi belum hitung ulang)
    public function updatedSelectedLibraryId()
    {
        if ($this->projectId && $this->selectedLibraryId) {
            $project = Project::find($this->projectId);
            $project->update(['cost_library_id' => $this->selectedLibraryId]);
        }
    }

    // 3. Fungsi Hitung (Tombol Calculate)
    public function calculateNow(CostEstimationService $service)
    {
        if (!$this->projectId || !$this->selectedLibraryId) {
            session()->flash('error', 'Please select both Project and Cost Library.');
            return;
        }

        $project = Project::find($this->projectId);
        $output = $service->calculateProject($project);

        if ($output['status'] == 'success') {
            session()->flash('message', 'Calculation Complete! Total: Rp ' . number_format($output['total_cost']));
            $this->loadResults();
        } else {
            session()->flash('error', $output['message']);
        }
    }

    // 4. Load Data & Grouping BOQ (Fitur Request Anda)
    public function loadResults()
    {
        if (!$this->projectId) {
            $this->resetResults();
            return;
        }

        // Ambil Data Mentah
        $rawResults = CostResult::with(['element'])
            ->where('project_id', $this->projectId)
            ->get();

        if ($rawResults->isEmpty()) {
            $this->resetResults();
            return;
        }

        // Hitung Summary
        $this->summary = [
            'total_cost' => $rawResults->sum('total_cost'),
            'matched' => $rawResults->where('status', 'matched')->count(),
            'unassigned' => $rawResults->where('status', 'unassigned')->count(),
        ];
        $this->unassignedCount = $this->summary['unassigned'];

        // --- LOGIKA GROUPING BOQ (HIERARKI) ---
        // Kita perlu join dengan tabel AHSP Master untuk dapat nama Divisi
        // Karena CostResult cuma simpan kode (C2010), kita perlu info detailnya
        
        // Ambil Detail AHSP yang terpakai di project ini
        $project = Project::find($this->projectId);
        if(!$project->cost_library_id) return;

        $ahspDetails = \App\Models\AhspMaster::where('cost_library_id', $project->cost_library_id)
                        ->get()
                        ->keyBy('code');

        // Lakukan Grouping Manual di PHP Collection
        $grouped = $rawResults->where('status', 'matched')->groupBy(function($item) use ($ahspDetails) {
            // Group Level 1: DIVISION (Contoh: STRUKTUR)
            $code = $item->matched_work_code;
            return $ahspDetails[$code]->division ?? 'GENERAL WORK';
        });

        // Format ulang struktur data untuk View
        $this->groupedResults = $grouped->map(function($itemsInDivision) use ($ahspDetails) {
            // Group Level 2: AHSP Item (Contoh: Pek. Beton K300)
            return $itemsInDivision->groupBy('matched_work_code')->map(function($itemsInCode) use ($ahspDetails) {
                $code = $itemsInCode->first()->matched_work_code;
                $ahsp = $ahspDetails[$code] ?? null;
                
                return [
                    'code' => $code,
                    'name' => $ahsp ? $ahsp->name : 'Unknown Item',
                    'unit' => $ahsp ? $ahsp->unit : '-',
                    'sub_division' => $ahsp ? $ahsp->sub_division : '',
                    'volume' => $itemsInCode->sum('element.volume'),
                    'unit_price' => $itemsInCode->first()->unit_price_applied,
                    'total_price' => $itemsInCode->sum('total_cost'),
                    'count' => $itemsInCode->count() // Jumlah elemen dinding/kolom
                ];
            });
        });
    }

    public function exportExcel()
    {
        if (!$this->projectId) return;
        $project = Project::find($this->projectId);
        $filename = 'BOQ_' . str_replace(' ', '_', $project->name) . '.xlsx';
        
        return Excel::download(new ProjectBoqExport($this->projectId), $filename);
    }

    private function resetResults() {
        $this->summary = null;
        $this->groupedResults = [];
        $this->unassignedCount = 0;
    }

    public function render()
    {
        return view('livewire.project-cost-calculator', [
            'projects' => Project::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get(),
            'libraries' => CostLibrary::where('user_id', Auth::id())->get()
        ])->layout('layouts.app');
    }
}