<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelElement extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'raw_properties' => 'array',
    ];

    // Relasi ke Project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}