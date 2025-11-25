<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterUnitPrice extends Model
{
    use HasFactory;

    // Guarded kosong artinya semua kolom boleh diisi massal
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}