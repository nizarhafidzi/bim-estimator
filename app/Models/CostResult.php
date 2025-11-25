<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostResult extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    // Relasi ke tabel elements
    public function element()
    {
        return $this->belongsTo(ModelElement::class, 'model_element_id');
    }
}