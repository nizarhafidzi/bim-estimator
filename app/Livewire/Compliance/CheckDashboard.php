<?php

namespace App\Livewire\Compliance;

use App\Models\ProjectFile;
use App\Models\ValidationResult;
use App\Services\AutodeskService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CheckDashboard extends Component
{
    use WithPagination;

    public $fileId;
    public $file;
    
    // PROPERTI PENTING YANG HILANG
    public $viewerToken; 
    
    public $filterStatus = 'fail'; 
    public $searchRule = '';

    // Data untuk JS
    public $failedIds = [];
    public $passedIds = [];

    public function mount($fileId)
    {
        $this->file = ProjectFile::findOrFail($fileId);
        $this->fileId = $fileId;
        
        // 1. AMBIL TOKEN (WAJIB AGAR VIEWER JALAN)
        $service = app(AutodeskService::class);
        $this->viewerToken = $service->getValidUserToken(Auth::user());
        // -------------------------------------------
    }
    public function render()
    {
        // 1. Statistik Ringkas
        $stats = [
            'total' => ValidationResult::where('project_file_id', $this->fileId)->count(),
            'pass' => ValidationResult::where('project_file_id', $this->fileId)->where('status', 'pass')->count(),
            'fail' => ValidationResult::where('project_file_id', $this->fileId)->where('status', 'fail')->count(),
        ];

        // 2. Query Data Tabel
        $query = ValidationResult::with(['rule', 'element'])
                    ->where('project_file_id', $this->fileId);

        $allResults = ValidationResult::with('element')
                        ->where('project_file_id', $this->fileId)
                        ->get();

        $this->failedIds = [];
        $this->passedIds = [];

        foreach ($allResults as $res) {
            if ($res->element) {
                $guid = $res->element->external_id;
                if ($res->status == 'fail') {
                    $this->failedIds[] = $guid;
                } else {
                    $this->passedIds[] = $guid;
                }
            }
        }

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->searchRule) {
            $query->whereHas('rule', function($q) {
                $q->where('parameter', 'like', '%' . $this->searchRule . '%')
                  ->orWhere('category_target', 'like', '%' . $this->searchRule . '%');
            });
        }

        $results = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('livewire.compliance.check-dashboard', [
            'stats' => [
                'total' => ValidationResult::where('project_file_id', $this->fileId)->count(),
                'pass' => ValidationResult::where('project_file_id', $this->fileId)->where('status', 'pass')->count(),
                'fail' => ValidationResult::where('project_file_id', $this->fileId)->where('status', 'fail')->count(),
            ],
            'results' => $results
        ])->layout('layouts.app');
    }
}