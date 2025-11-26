<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. BERSIHKAN TABEL LAMA (Agar tidak konflik saat migrasi ulang)
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('cost_results');
        Schema::dropIfExists('model_elements');
        Schema::dropIfExists('project_files'); // Tabel baru
        Schema::dropIfExists('projects');      // Tabel lama yang akan dirombak
        Schema::enableForeignKeyConstraints();

        // 2. TABEL PROJECTS (HEADER / WADAH GABUNGAN)
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Referensi ke Buku Harga (Satu proyek pakai 1 standar harga)
            $table->foreignId('cost_library_id')->nullable()->constrained('cost_libraries')->nullOnDelete();
            
            $table->string('name'); // Contoh: "Pembangunan RSUD Type B"
            $table->string('acc_project_id')->nullable(); // ID Folder Root di Autodesk ACC
            
            $table->timestamps();
        });

        // 3. TABEL PROJECT FILES (ANAK - FILE REVIT)
        // Satu Project bisa punya banyak File (Arsitek.rvt, Struktur.rvt, MEP.rvt)
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            
            $table->string('name'); // Contoh: "Struktur_Lt1-5.rvt"
            $table->string('urn');  // ID Model Autodesk (Base64 URN)
            
            // Status Import per File
            $table->enum('status', ['processing', 'ready', 'error'])->default('processing');
            $table->text('error_message')->nullable();
            $table->json('debug_logs')->nullable(); // Log proses import per file
            
            $table->timestamps();
        });

        // 4. TABEL MODEL ELEMENTS (DATA 3D)
        Schema::create('model_elements', function (Blueprint $table) {
            $table->id();
            
            // Relasi Cepat ke Project Header
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            
            // RELASI BARU: Elemen ini milik File yang mana?
            $table->foreignId('project_file_id')->constrained('project_files')->cascadeOnDelete();
            
            $table->string('external_id')->index(); // GUID (String)
            $table->string('category')->nullable(); // Walls, Floors
            $table->string('name')->nullable();     // Basic Wall: Generic 200mm
            $table->string('assembly_code')->nullable()->index(); // Keynote/Code AHSP
            
            $table->decimal('volume', 15, 4)->default(0);
            
            // Menyimpan semua parameter asli Revit (JSON)
            $table->json('raw_properties')->nullable();
            
            $table->timestamps();
        });

        // 5. TABEL COST RESULTS (HASIL HITUNGAN)
        Schema::create('cost_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            
            // Relasi ke Elemen Spesifik
            $table->foreignId('model_element_id')->constrained('model_elements')->cascadeOnDelete();
            
            $table->string('matched_work_code')->nullable(); // Kode AHSP yang ketemu
            $table->decimal('unit_price_applied', 15, 2)->default(0); // Harga Satuan
            $table->decimal('total_cost', 15, 2)->default(0); // Vol x Harga
            
            $table->string('status')->default('unassigned'); // matched / unassigned
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('cost_results');
        Schema::dropIfExists('model_elements');
        Schema::dropIfExists('project_files');
        Schema::dropIfExists('projects');
        Schema::enableForeignKeyConstraints();
    }
};