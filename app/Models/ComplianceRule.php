<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_set_id',
        'category_target',
        'parameter',
        'operator',
        'value',
        'severity',
        'description',
    ];

    // Relasi: Milik RuleSet apa
    public function ruleSet()
    {
        return $this->belongsTo(RuleSet::class);
    }

    // Relasi: Aturan ini menghasilkan banyak validasi result
    public function results()
    {
        return $this->hasMany(ValidationResult::class, 'rule_id');
    }
}