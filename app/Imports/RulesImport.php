<?php

namespace App\Imports;

use App\Models\ComplianceRule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RulesImport implements ToModel, WithHeadingRow
{
    protected $ruleSetId;

    public function __construct($ruleSetId)
    {
        $this->ruleSetId = $ruleSetId;
    }

    public function model(array $row)
    {
        // Pastikan kolom Excel tidak kosong
        if (!isset($row['category']) || !isset($row['parameter'])) {
            return null;
        }

        return new ComplianceRule([
            'rule_set_id'     => $this->ruleSetId,
            'category_target' => $row['category'],     // Header Excel: Category
            'parameter'       => $row['parameter'],    // Header Excel: Parameter
            'operator'        => $row['operator'] ?? '=', // Default '='
            'value'           => (string) $row['value'],
            'severity'        => strtolower($row['severity'] ?? 'error'), // error/warning
            'description'     => $row['description'] ?? null,
        ]);
    }
}