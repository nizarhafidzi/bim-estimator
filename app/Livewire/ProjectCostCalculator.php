<?php

namespace App\Livewire;

use App\Models\CostResult;
use App\Models\Project;
use App\Services\CostEstimationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjectCostCalculator extends Component
{
    public $projectId;
    public $summary = null;
    public $results = [];
    public $isCalculating = false;

    // Load Estimasi saat Project dipilih
    public function updatedProjectId()
    {
        $this->loadResults();
    }

    public function loadResults()
    {
        if (!$this->projectId) {
            $this->summary = null;
            $this->results = [];
            return;
        }

        // Ambil Data Existing dari Database
        $this->results = CostResult::with(['element'])
            ->where('project_id', $this->projectId)
            ->get();

        // Hitung Summary Cepat
        if ($this->results->isNotEmpty()) {
            $this->summary = [
                'total_cost' => $this->results->sum('total_cost'),
                'matched' => $this->results->where('status', 'matched')->count(),
                'unassigned' => $this->results->where('status', 'unassigned')->count(),
            ];
        } else {
            $this->summary = null;
        }
    }

    public function calculateNow(CostEstimationService $service)
    {
        if (!$this->projectId) return;

        $this->isCalculating = true;
        
        $project = Project::find($this->projectId);
        
        // Panggil Service yang baru kita buat
        $output = $service->calculateProject($project);

        $this->isCalculating = false;

        if ($output['status'] == 'success') {
            $this->loadResults(); // Reload tampilan
            session()->flash('message', 'Perhitungan Selesai! Total Biaya: Rp ' . number_format($output['total_cost']));
        } else {
            session()->flash('error', 'Gagal menghitung: ' . $output['message']);
        }
    }

    public function render()
    {
        return view('livewire.project-cost-calculator', [
            'projects' => Project::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get()
        ])->layout('layouts.app');
    }
}