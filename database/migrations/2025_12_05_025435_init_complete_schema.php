<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- 1. TABEL CORE (USERS & AUTH) ---
        
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Kolom Autodesk Integration
            $table->text('autodesk_access_token')->nullable();
            $table->text('autodesk_refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('autodesk_account_name')->nullable();
            
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // --- 2. TABEL COST ESTIMATOR (MASTER DATA) ---

        // Buku Harga (Library)
        Schema::create('cost_libraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name'); 
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Bahan Baku (Semen, Pasir, Upah)
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_library_id')->constrained('cost_libraries')->cascadeOnDelete();
            $table->string('resource_code')->index(); 
            $table->string('type')->default('material'); // material/manpower/equipment
            $table->string('name'); 
            $table->string('unit'); 
            $table->decimal('price', 15, 2)->default(0); 
            $table->timestamps();
        });

        // Analisa Harga Satuan (AHSP Header)
        Schema::create('ahsp_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_library_id')->constrained('cost_libraries')->cascadeOnDelete();
            $table->string('code')->index(); // Assembly Code
            $table->string('name'); 
            $table->string('division')->nullable(); // STRUKTUR/ARSITEKTUR
            $table->string('sub_division')->nullable(); 
            $table->string('unit'); 
            $table->timestamps();
        });

        // Rumus AHSP (Koefisien)
        Schema::create('ahsp_coefficients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ahsp_master_id')->constrained('ahsp_masters')->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->decimal('coefficient', 10, 4); 
            $table->timestamps();
        });

        // --- 3. TABEL PROJECT STRUCTURE (FEDERATED) ---

        // Project Header (Wadah)
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // Link ke Library Harga yang dipakai
            $table->foreignId('cost_library_id')->nullable()->constrained('cost_libraries')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Project Files (File Revit Asli)
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name'); // Nama File
            $table->string('urn');  // ID Autodesk
            $table->enum('status', ['processing', 'ready', 'error'])->default('processing');
            $table->text('error_message')->nullable();
            $table->json('debug_logs')->nullable();
            $table->timestamps();
        });

        // Model Elements (Data 3D yang sudah di-extract)
        Schema::create('model_elements', function (Blueprint $table) {
            $table->id();
            // Relasi Utama: Ke File
            $table->foreignId('project_file_id')->constrained('project_files')->cascadeOnDelete();
            // Relasi Shortcut: Ke Project Header
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            
            $table->string('external_id')->index(); // GUID
            $table->string('category')->nullable();
            $table->string('name')->nullable();
            $table->string('assembly_code')->nullable();
            $table->decimal('volume', 15, 4)->default(0);
            $table->json('raw_properties')->nullable(); // Data lengkap JSON
            
            $table->timestamps();
        });

        // Hasil Perhitungan Biaya (Cost Results)
        Schema::create('cost_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('model_element_id')->constrained('model_elements')->cascadeOnDelete();
            
            $table->string('matched_work_code')->nullable();
            $table->decimal('unit_price_applied', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->string('status')->default('unassigned');
            
            $table->timestamps();
        });

        // --- 4. TABEL COMPLIANCE CHECKER (MODUL BARU) ---

        // Rule Sets (Kumpulan Aturan)
        Schema::create('rule_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        // Detail Aturan
        Schema::create('compliance_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_set_id')->constrained('rule_sets')->cascadeOnDelete();
            $table->string('category_target');
            $table->string('parameter');
            $table->string('operator');
            $table->string('value');
            $table->enum('severity', ['error', 'warning'])->default('error');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Hasil Validasi
        Schema::create('validation_results', function (Blueprint $table) {
            $table->id();
            // Relasi Shortcut
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('project_file_id')->constrained('project_files')->cascadeOnDelete();
            
            $table->foreignId('model_element_id')->constrained('model_elements')->cascadeOnDelete();
            $table->foreignId('rule_id')->constrained('compliance_rules')->cascadeOnDelete();
            
            $table->enum('status', ['pass', 'fail']);
            $table->string('actual_value')->nullable();
            $table->text('message')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        // Drop urut dari anak ke induk
        Schema::dropIfExists('validation_results');
        Schema::dropIfExists('compliance_rules');
        Schema::dropIfExists('rule_sets');
        Schema::dropIfExists('cost_results');
        Schema::dropIfExists('model_elements');
        Schema::dropIfExists('project_files');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('ahsp_coefficients');
        Schema::dropIfExists('ahsp_masters');
        Schema::dropIfExists('resources');
        Schema::dropIfExists('cost_libraries');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();
    }
};