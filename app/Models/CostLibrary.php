<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostLibrary extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Library punya banyak Resource
    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    // Library punya banyak Analisa (AHSP)
    public function ahsps()
    {
        return $this->hasMany(AhspMaster::class);
    }
}