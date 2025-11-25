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
        // 1. Tabel Cost Library (Buku Harga per Proyek/Standar)
        Schema::create('cost_libraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Contoh: "SNI DKI Jakarta 2025" atau "Proyek Rumah A"
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Resources (Bahan & Upah Dasar)
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_library_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // 'material', 'labor', 'equipment'
            $table->string('name'); // Semen Portland, Tukang Batu
            $table->string('unit'); // kg, oh, jam
            $table->decimal('price', 15, 2); // Harga Dasar
            $table->timestamps();
        });

        // 3. Tabel AHSP Master (Item Pekerjaan Jadi)
        // Ini yang kodenya akan dicocokkan dengan Assembly Code Revit
        Schema::create('ahsp_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_library_id')->constrained()->cascadeOnDelete();
            
            $table->string('code')->index(); // C2010 (Sama dgn Revit Assembly Code)
            $table->string('name'); // Pek. Beton K-300
            
            // Grouping untuk BOQ (Sesuai request kamu: Disiplin/Divisi)
            $table->string('division')->nullable(); // STRUKTUR, ARSITEKTUR
            $table->string('sub_division')->nullable(); // PEKERJAAN BETON
            
            $table->string('unit'); // m3
            $table->timestamps();
        });

        // 4. Tabel AHSP Coefficients (Resep/Rumus)
        // Menghubungkan AHSP Master dengan Resources
        Schema::create('ahsp_coefficients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ahsp_master_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            $table->decimal('coefficient', 10, 4); // Koefisien (misal: 0.85 sak semen)
            $table->timestamps();
        });

        // 5. Update Tabel Projects (Agar project punya referensi Library)
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('cost_library_id')->nullable()->constrained('cost_libraries')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ahsp_structure_tables');
    }
};
