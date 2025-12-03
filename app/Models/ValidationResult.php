<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_file_id',
        'rule_id',
        'model_element_id',
        'status',
        'actual_value',
        'message',
    ];

    // Relasi ke File
    public function file()
    {
        return $this->belongsTo(ProjectFile::class, 'project_file_id');
    }

    // Relasi ke Rule
    public function rule()
    {
        return $this->belongsTo(ComplianceRule::class, 'rule_id');
    }

    // Relasi ke Elemen BIM
    public function element()
    {
        return $this->belongsTo(ModelElement::class, 'model_element_id');
    }
}