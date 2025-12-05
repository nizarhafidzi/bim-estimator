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
    public $viewerToken;
    
    // Filter UI
    public $filterStatus = 'fail'; 
    public $searchRule = '';

    // Data JS
    public $failedIds = [];
    public $passedIds = [];
    
    // Statistik
    public $stats = [
        'total' => 0,
        'pass' => 0,
        'fail' => 0
    ];

    public function mount($fileId)
    {
        $this->fileId = $fileId;
        $this->file = ProjectFile::findOrFail($fileId);

        // 1. Get Token
        $service = app(AutodeskService::class);
        $this->viewerToken = $service->getValidUserToken(Auth::user());

        // 2. Hitung Statistik (Query Ringan)
        // Gunakan where langsung ke DB, bukan collection count
        $this->stats['total'] = ValidationResult::where('project_file_id', $fileId)->count();
        $this->stats['pass'] = ValidationResult::where('project_file_id', $fileId)->where('status', 'pass')->count();
        $this->stats['fail'] = ValidationResult::where('project_file_id', $fileId)->where('status', 'fail')->count();

        // 3. Ambil Semua ID untuk Pewarnaan 3D (Query Khusus ID saja biar cepat)
        // Select spesifik kolom agar hemat memori
        $allResults = ValidationResult::where('project_file_id', $fileId)
                        ->with('element:id,external_id') // Eager load element ID only
                        ->select('id', 'model_element_id', 'status')
                        ->get();

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
    }

    public function render()
    {
        // 4. Query Tabel (Paginated)
        // Mulai dari Model::query() agar objeknya Builder, bukan Collection
        $query = ValidationResult::query()
                    ->with(['rule', 'element']) // Eager Load
                    ->where('project_file_id', $this->fileId);

        // Filter Status
        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        // Filter Search
        if (!empty($this->searchRule)) {
            $search = $this->searchRule;
            $query->whereHas('rule', function($q) use ($search) {
                $q->where('parameter', 'like', '%' . $search . '%')
                  ->orWhere('category_target', 'like', '%' . $search . '%');
            });
            // Optional: Search by Element Name juga
            $query->orWhereHas('element', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        // Pagination
        $results = $query->orderBy('id', 'asc')->paginate(20);

        return view('livewire.compliance.check-dashboard', [
            'results' => $results
        ])->layout('layouts.app');
    }
}