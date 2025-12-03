<?php

namespace App\Livewire\Compliance;

use App\Models\ProjectFile;
use App\Models\ValidationResult;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ComplianceReport extends Component
{
    public $fileId;
    public $file;
    public $project;
    public $currentDate;
    
    public $stats = [];
    public $results = [];

    public function mount($fileId)
    {
        $this->fileId = $fileId;
        $this->file = ProjectFile::with('project')->findOrFail($fileId);
        $this->project = $this->file->project;
        $this->currentDate = now()->format('d F Y');

        // 1. Statistik
        $total = ValidationResult::where('project_file_id', $fileId)->count();
        $pass = ValidationResult::where('project_file_id', $fileId)->where('status', 'pass')->count();
        $fail = ValidationResult::where('project_file_id', $fileId)->where('status', 'fail')->count();
        
        $this->stats = [
            'total' => $total,
            'pass' => $pass,
            'fail' => $fail,
            'score' => $total > 0 ? round(($pass / $total) * 100, 1) : 0
        ];

        // 2. Data Detail (Group by Rule Category)
        $this->results = ValidationResult::with(['rule', 'element'])
            ->where('project_file_id', $fileId)
            ->get()
            ->groupBy(function($item) {
                return $item->rule->category_target ?? 'General';
            })->sortKeys();
    }

    public function exportExcel()
    {
        $filename = 'Compliance_Report_' . $this->file->name . '_' . date('Ymd') . '.xlsx';
        return Excel::download(new ComplianceReportExport($this->fileId), $filename);
    }

    public function render()
    {
        return view('livewire.compliance.compliance-report')->layout('layouts.app');
    }
}