<?php

namespace App\Livewire\Compliance;

use App\Models\ProjectFile;
use App\Models\ValidationResult;
use Livewire\Component;

class ComplianceReport extends Component
{
    public $fileId;
    public $file;
    public $project;
    public $currentDate;
    
    public $stats = [];
    
    // Kita tidak kirim object complex, tapi array results yang sudah digroup
    public $groupedResults = [];

    public function mount($fileId)
    {
        $this->fileId = $fileId;
        $this->file = ProjectFile::with('project')->findOrFail($fileId);
        $this->project = $this->file->project;
        $this->currentDate = now()->format('d F Y');

        // 1. Statistik (Hitung langsung di DB agar cepat)
        $this->stats = [
            'total' => ValidationResult::where('project_file_id', $fileId)->count(),
            'pass'  => ValidationResult::where('project_file_id', $fileId)->where('status', 'pass')->count(),
            'fail'  => ValidationResult::where('project_file_id', $fileId)->where('status', 'fail')->count(),
        ];
        
        $this->stats['score'] = $this->stats['total'] > 0 
            ? round(($this->stats['pass'] / $this->stats['total']) * 100, 1) 
            : 0;

        // 2. Data Detail (Eager Load Relasi)
        $rawData = ValidationResult::with(['rule', 'element'])
            ->where('project_file_id', $fileId)
            ->get(); // Eksekusi query jadi Collection

        // 3. Grouping di PHP (Aman)
        // Kita group berdasarkan nama kategori target pada Rule
        $this->groupedResults = $rawData->groupBy(function($item) {
            return $item->rule->category_target ?? 'General';
        })->sortKeys();
    }

    // Fungsi Export Excel
    public function exportExcel()
    {
        $filename = 'Compliance_Report_' . str_replace(' ', '_', $this->file->name) . '_' . date('Ymd') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ComplianceReportExport($this->fileId), $filename);
    }

    public function render()
    {
        return view('livewire.compliance.compliance-report')->layout('layouts.app');
    }
}