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
        Schema::create('model_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete(); // Relasi ke tabel projects lokal
            $table->string('external_id')->index(); // ID unik dari Autodesk (ObjectId)
            $table->string('category')->nullable(); // Contoh: Walls, Floors
            $table->string('name')->nullable();     // Contoh: Basic Wall Generic 200mm
            $table->string('assembly_code')->nullable()->index(); // Kode Analisa Harga (PENTING)
            $table->decimal('volume', 15, 4)->default(0); // Volume m3
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_elements');
    }
};
