<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelElement extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'raw_properties' => 'array', // Agar JSON properti terbaca array
    ];

    // Relasi ke Project Header (Gedung)
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // --- RELASI BARU ---
    // Relasi ke File Asal (Misal: File Struktur.rvt)
    public function file()
    {
        return $this->belongsTo(ProjectFile::class, 'project_file_id');
    }

    // Relasi ke history validasi elemen ini
    public function validationResults()
    {
        return $this->hasMany(ValidationResult::class);
    }
}