<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    // TAMBAHKAN INI
    protected $casts = [
        'debug_logs' => 'array', // Otomatis convert JSON <-> Array
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function elements()
    {
        return $this->hasMany(ModelElement::class);
    }
}