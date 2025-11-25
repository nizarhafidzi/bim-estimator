<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cost_results', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Project
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            
            // Relasi ke Elemen Model (Dinding/Lantai yang dihitung)
            $table->foreignId('model_element_id')->constrained('model_elements')->cascadeOnDelete();
            
            // Kolom Hasil Perhitungan
            $table->string('matched_work_code')->nullable(); // Kode analisa yang ketemu
            $table->decimal('unit_price_applied', 15, 2)->default(0); // Harga satuan yang dipakai
            $table->decimal('total_cost', 15, 2)->default(0); // Total (Volume x Harga)
            
            $table->string('status')->default('unassigned'); // matched / unassigned
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_results');
    }
};
