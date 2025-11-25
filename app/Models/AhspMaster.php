<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhspMaster extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Relasi ke Koefisien (Bumbu-bumbunya)
    public function coefficients()
    {
        return $this->hasMany(AhspCoefficient::class);
    }

    // ACCESSOR PENTING: Menghitung Total Harga Satuan
    // Cara pakainya nanti: $ahsp->total_price
    public function getTotalPriceAttribute()
    {
        $total = 0;
        // Loop semua bahan penyusun
        foreach ($this->coefficients as $coef) {
            if ($coef->resource) {
                // Rumus: Koefisien x Harga Resource
                $total += ($coef->coefficient * $coef->resource->price);
            }
        }
        return $total;
    }
}