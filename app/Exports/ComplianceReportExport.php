<?php

namespace App\Exports;

use App\Models\ValidationResult;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ComplianceReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $fileId;

    public function __construct($fileId)
    {
        $this->fileId = $fileId;
    }

    public function collection()
    {
        return ValidationResult::with(['element', 'rule'])
            ->where('project_file_id', $this->fileId)
            ->orderBy('status', 'asc') // Fail dulu baru Pass
            ->get();
    }

    public function map($row): array
    {
        return [
            strtoupper($row->status),
            $row->element->category ?? '-',
            $row->element->name ?? 'Unknown',
            $row->element->external_id,
            $row->rule->parameter ?? '-',
            $row->rule->operator . ' ' . $row->rule->value,
            $row->actual_value,
            $row->message
        ];
    }

    public function headings(): array
    {
        return [
            'Status',
            'Category',
            'Element Name',
            'Revit ID (GUID)',
            'Parameter Checked',
            'Rule Condition',
            'Actual Value Found',
            'Validation Message'
        ];
    }
}