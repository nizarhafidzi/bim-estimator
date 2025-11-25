<?php

namespace App\Livewire;

use App\Models\CostResult;
use App\Models\Project;
use App\Models\AhspMaster;
use App\Services\AutodeskService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;

class ProjectDashboard extends Component
{
    public $projectId;
    public $project;
    
    // Data untuk Viewer JS
    public $viewerToken;
    public $viewerUrn;
    public $matchedIds = [];
    public $unassignedIds = [];
    
    // Data Interaksi (Tooltip & Filter)
    public $elementData = []; // Key: GUID, Value: Detail Info
    public $workCodeMap = []; // Key: WorkCode, Value: Array of GUIDs
    
    // Data Sidebar (BOQ)
    public $groupedBoq = [];  
    public $totalCost = 0;

    public function mount($id)
    {
        $this->projectId = $id;
        $this->project = Project::findOrFail($id);
        
        // 1. Get Token & URN
        $service = app(AutodeskService::class);
        $this->viewerToken = $service->getValidUserToken(Auth::user());
        $this->viewerUrn = $this->project->urn;

        // 2. Ambil Referensi AHSP (Untuk Nama Pekerjaan & Divisi)
        $ahspLookup = [];
        if ($this->project->cost_library_id) {
            $ahspLookup = AhspMaster::where('cost_library_id', $this->project->cost_library_id)
                            ->get()
                            ->keyBy('code');
        }

        // 3. Ambil Hasil Hitungan
        $results = CostResult::with('element')
                    ->where('project_id', $id)
                    ->get();

        $this->totalCost = $results->sum('total_cost');

        // 4. Loop Data untuk Viewer
        foreach ($results as $res) {
            if ($res->element) {
                $guid = $res->element->external_id; // GUID String (Stable ID)

                // A. Siapkan Data Tooltip
                $ahsp = isset($ahspLookup[$res->matched_work_code]) ? $ahspLookup[$res->matched_work_code] : null;
                
                $this->elementData[$guid] = [
                    'status' => $res->status,
                    'revit_name' => Str::beforeLast($res->element->name, '['),
                    'cost_formatted' => 'Rp ' . number_format($res->total_cost, 0, ',', '.'),
                    'work_name' => $ahsp ? $ahsp->name : 'Unknown Work Item',
                    'work_code' => $res->matched_work_code ?? '-',
                    'division' => $ahsp ? $ahsp->division : 'General',
                ];

                // B. Pisahkan ID untuk Pewarnaan Default
                if ($res->status == 'matched') {
                    $this->matchedIds[] = $guid;
                    
                    // C. Mapping untuk Fitur Klik Sidebar (Filter)
                    $code = $res->matched_work_code;
                    if (!isset($this->workCodeMap[$code])) {
                        $this->workCodeMap[$code] = [];
                    }
                    $this->workCodeMap[$code][] = $guid;

                } else {
                    $this->unassignedIds[] = $guid;
                }
            }
        }

        // 5. Siapkan Data Sidebar (Grouping BOQ)
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