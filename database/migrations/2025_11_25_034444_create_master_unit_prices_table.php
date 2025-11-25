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
        Schema::create('master_unit_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // work_code ini yang akan dicocokkan dengan Assembly Code di Revit
            $table->string('work_code')->index(); 
            
            $table->string('description'); // Nama Pekerjaan (misal: Beton K300)
            $table->decimal('price', 15, 2); // Harga (Rp)
            $table->string('unit'); // Satuan (m3, m2, kg)
            $table->timestamps();
            
            // Mencegah duplikat kode harga untuk user yang sama
            $table->unique(['user_id', 'work_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_unit_prices');
    }
};
