<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectFile extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Cast JSON log agar bisa dibaca sebagai Array di PHP
    protected $casts = [
        'debug_logs' => 'array',
    ];

    // Relasi: File ini milik Project Header yang mana?
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Relasi: File ini punya banyak elemen (Dinding, Kolom, dll)
    public function elements()
    {
        return $this->hasMany(ModelElement::class);
    }

    // Relasi ke Hasil Validasi (Modul Compliance)
    public function validationResults()
    {
        return $this->hasMany(ValidationResult::class);
    }
}