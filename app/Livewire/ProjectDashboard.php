<?php

namespace App\Livewire;

use App\Models\CostResult;
use App\Models\Project;
use App\Services\AutodeskService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjectDashboard extends Component
{
    public $projectId;
    public $project;
    
    // Viewer Data
    public $viewerToken;
    public $viewerUrn;
    public $matchedIds = [];
    public $unassignedIds = [];
    
    // Data Baru: Untuk Tooltip & Summary Breakdown
    public $elementCosts = []; // Key: GUID, Value: Formatted Price
    public $costBreakdown = []; // List Assembly Code & Totalnya

    public function mount($id)
    {
        $this->projectId = $id;
        $this->project = Project::findOrFail($id);
        
        $service = app(AutodeskService::class);
        $this->viewerToken = $service->getValidUserToken(Auth::user());
        $this->viewerUrn = $this->project->urn;

        // 1. Ambil Data Cost
        $results = CostResult::with('element')
                    ->where('project_id', $id)
                    ->get();

        foreach ($results as $res) {
            if ($res->element) {
                $guid = $res->element->external_id;

                // Pisahkan ID untuk pewarnaan
                if ($res->status == 'matched') {
                    $this->matchedIds[] = $guid;
                    
                    // Simpan Harga untuk Tooltip (Hanya yang matched)
                    // Format: "Rp 1.500.000"
                    $this->elementCosts[$guid] = [
                        'cost' => 'Rp ' . number_format($res->total_cost, 0, ',', '.'),
                        'code' => $res->matched_work_code,
                        'name' => \Illuminate\Support\Str::beforeLast($res->element->name, '[')
                    ];
                } else {
                    $this->unassignedIds[] = $guid;
                    $this->elementCosts[$guid] = [
                        'cost' => 'Unassigned',
                        'code' => '-',
                        'name' => \Illuminate\Support\Str::beforeLast($res->element->name, '[')
                    ];
                }
            }
        }

        // 2. Ambil Breakdown Summary (Group by Assembly Code)
        $this->costBreakdown = CostResult::where('project_id', $id)
            ->where('status', 'matched')
            ->selectRaw('matched_work_code, sum(total_cost) as total')
            ->groupBy('matched_work_code')
            ->orderBy('total', 'desc') // Urutkan dari yang termahal
            ->get();
    }

    public function render()
    {
        return view('livewire.project-dashboard')->layout('layouts.app');
    }
}