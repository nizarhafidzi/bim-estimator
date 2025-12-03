<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relasi: Milik User siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Menggunakan Standar Harga (Library) yang mana?
    public function costLibrary()
    {
        return $this->belongsTo(CostLibrary::class);
    }

    // --- RELASI BARU ---
    // Project punya banyak File Revit (Arsitek, Struktur, dll)
    public function files()
    {
        return $this->hasMany(ProjectFile::class);
    }
    
    // Project punya banyak Elemen (Shortcut relation)
    // Karena di tabel model_elements kita simpan project_id, kita bisa akses langsung
    public function elements()
    {
        return $this->hasMany(ModelElement::class);
    }
    
    // Project punya banyak Hasil Hitungan
    public function costResults()
    {
        return $this->hasMany(CostResult::class);
    }

    // Relasi ke Rule Sets (Modul Compliance)
    public function ruleSets()
    {
        return $this->hasMany(RuleSet::class);
    }
}