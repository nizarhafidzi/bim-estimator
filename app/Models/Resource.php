<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function library()
    {
        return $this->belongsTo(CostLibrary::class, 'cost_library_id');
    }
}