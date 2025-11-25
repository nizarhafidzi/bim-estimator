<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhspCoefficient extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Milik AHSP siapa?
    public function ahsp()
    {
        return $this->belongsTo(AhspMaster::class, 'ahsp_master_id');
    }

    // Menggunakan bahan apa?
    public function resource()
    {
        return $this->belongsTo(Resource::class, 'resource_id');
    }
}