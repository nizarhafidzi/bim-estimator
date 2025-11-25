<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- BAGIAN PEMBERSIHAN (ANTI ERROR "TABLE EXISTS") ---
        // Kita matikan pengecekan kunci tamu sebentar agar bisa hapus tabel sembarangan
        Schema::disableForeignKeyConstraints();
        
        Schema::dropIfExists('ahsp_coefficients');
        Schema::dropIfExists('ahsp_masters');
        Schema::dropIfExists('resources');
        Schema::dropIfExists('cost_libraries');
        Schema::dropIfExists('master_unit_prices'); // Hapus tabel versi lama
        
        Schema::enableForeignKeyConstraints();
        // -------------------------------------------------------

        // 1. Tabel Cost Library (Wadah Buku Harga)
        Schema::create('cost_libraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name'); 
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Resources (Bahan Baku & Upah)
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_library_id')->constrained('cost_libraries')->cascadeOnDelete();
            $table->string('resource_code')->index(); 
            $table->string('type'); // material/manpower/equipment
            $table->string('name'); 
            $table->string('unit'); 
            $table->decimal('price', 15, 2); 
            $table->timestamps();
        });

        // 3. Tabel AHSP Master (Header Analisa)
        Schema::create('ahsp_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_library_id')->constrained('cost_libraries')->cascadeOnDelete();
            
            $table->string('code')->index(); // Assembly Code
            $table->string('name'); 
            
            $table->string('division')->nullable(); // STRUKTUR/ARSITEKTUR
            $table->string('sub_division')->nullable(); // DINDING/LANTAI
            
            $table->string('unit'); 
            $table->timestamps();
        });

        // 4. Tabel AHSP Coefficients (Resep/Rumus)
        Schema::create('ahsp_coefficients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ahsp_master_id')->constrained('ahsp_masters')->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->decimal('coefficient', 10, 4); 
            $table->timestamps();
        });

        // 5. Update Tabel Projects (Menambah kolom cost_library_id)
        if (!Schema::hasColumn('projects', 'cost_library_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->foreignId('cost_library_id')->nullable()->constrained('cost_libraries')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        if (Schema::hasColumn('projects', 'cost_library_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropForeign(['cost_library_id']);
                $table->dropColumn('cost_library_id');
            });
        }
        Schema::dropIfExists('ahsp_coefficients');
        Schema::dropIfExists('ahsp_masters');
        Schema::dropIfExists('resources');
        Schema::dropIfExists('cost_libraries');
        Schema::enableForeignKeyConstraints();
    }
};