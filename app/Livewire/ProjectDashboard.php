<?php

namespace App\Livewire;

use App\Models\CostResult;
use App\Models\ProjectFile; // Pakai Model File
use App\Models\AhspMaster;
use App\Services\AutodeskService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;

class ProjectDashboard extends Component
{
    public $fileId;
    public $file;
    public $project; // Parent Project
    
    // Viewer Data
    public $viewerToken;
    public $viewerUrn;
    
    // Data Interaksi
    public $matchedIds = [];
    public $unassignedIds = [];
    public $elementData = []; 
    public $workCodeMap = []; 
    
    // Data Sidebar
    public $groupedBoq = [];  
    public $totalCost = 0;

    public function mount($fileId)
    {
        $this->fileId = $fileId;
        // Ambil File beserta Parent Project-nya
        $this->file = ProjectFile::with('project')->findOrFail($fileId);
        $this->project = $this->file->project;
        
        // Set URN Langsung
        $this->viewerUrn = $this->file->urn;

        // Get Token
        $service = app(AutodeskService::class);
        $this->viewerToken = $service->getValidUserToken(Auth::user());

        // Load Data Cost
        $this->loadData();
    }

    public function loadData()
    {
        // 1. Ambil AHSP Lookup
        $ahspLookup = [];
        if ($this->project->cost_library_id) {
            $ahspLookup = AhspMaster::where('cost_library_id', $this->project->cost_library_id)
                            ->get()
                            ->keyBy('code');
        }

        // 2. QUERY SPESIFIK: Hanya ambil cost result milik FILE INI
        // Kita filter via relasi model_element -> project_file_id
        $results = CostResult::with('element')
                    ->whereHas('element', function($q) {
                        $q->where('project_file_id', $this->fileId);
                    })
                    ->get();

        $this->totalCost = $results->sum('total_cost');

        // 3. Loop Data
        foreach ($results as $res) {
            if ($res->element) {
                $guid = $res->element->external_id;
                
                // Data Tooltip
                $ahsp = $ahspLookup[$res->matched_work_code] ?? null;
                
                $this->elementData[$guid] = [
                    'status' => $res->status,
                    'revit_name' => Str::beforeLast($res->element->name, '['),
                    'cost_formatted' => 'Rp ' . number_format($res->total_cost, 0, ',', '.'),
                    'work_name' => $ahsp ? $ahsp->name : 'Unknown',
                    'work_code' => $res->matched_work_code ?? '-',
                    'division' => $ahsp ? $ahsp->division : 'General',
                ];

                // Data Pewarnaan & Filter
                if ($res->status == 'matched') {
                    $this->matchedIds[] = $guid;
                    $code = $res->matched_work_code;
                    if (!isset($this->workCodeMap[$code])) $this->workCodeMap[$code] = [];
                    $this->workCodeMap[$code][] = $guid;
                } else {
                    $this->unassignedIds[] = $guid;
                }
            }
        }

        // 4. Grouping BOQ Sidebar
        $this->groupedBoq = $results->where('status', 'matched')
            ->groupBy(function($item) use ($ahspLookup) {
                $code = $item->matched_work_code;
                return $ahspLookup[$code]->division ?? 'GENERAL WORK';
            })
            ->map(function($itemsInDivision) use ($ahspLookup) {
                return $itemsInDivision->groupBy('matched_work_code')
                    ->map(function($itemsInCode) use ($ahspLookup) {
                        $code = $itemsInCode->first()->matched_work_code;
                        $ahsp = $ahspLookup[$code] ?? null;
                        return [
                            'code' => $code,
                            'name' => $ahsp ? $ahsp->name : 'Unknown',
                            'total' => $itemsInCode->sum('total_cost'),
                            'count' => $itemsInCode->count()
                        ];
                    })->sortByDesc('total');
            })->sortKeys();
    }

    public function render()
    {
        return view('livewire.project-dashboard')->layout('layouts.app');
    }
}