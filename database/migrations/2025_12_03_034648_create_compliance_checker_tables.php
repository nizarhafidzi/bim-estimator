<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rule Sets (Kumpulan Aturan / Header)
        Schema::create('rule_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name'); // Contoh: "Aturan Damkar 2024"
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Compliance Rules (Detail Aturan)
        Schema::create('compliance_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_set_id')->constrained('rule_sets')->cascadeOnDelete();
            
            $table->string('category_target'); // Contoh: "Walls", "Doors"
            $table->string('parameter');       // Contoh: "Fire Rating", "Unconnected Height"
            
            // Operator logika
            $table->enum('operator', ['>', '<', '=', '!=', '>=', '<=', 'contains'])->default('=');
            
            $table->string('value');           // Nilai pembanding (Threshold)
            $table->enum('severity', ['error', 'warning'])->default('error');
            $table->text('description')->nullable(); // Penjelasan aturan untuk user
            
            $table->timestamps();
        });

        // 3. Validation Results (Hasil Pengecekan per Elemen)
        Schema::create('validation_results', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke File (Agar tau ini hasil cek file mana)
            $table->foreignId('project_file_id')->constrained('project_files')->cascadeOnDelete();
            
            // Relasi ke Rule spesifik yang dicek
            $table->foreignId('rule_id')->constrained('compliance_rules')->cascadeOnDelete();
            
            // Relasi ke Elemen BIM spesifik
            $table->foreignId('model_element_id')->constrained('model_elements')->cascadeOnDelete();
            
            $table->enum('status', ['pass', 'fail']);
            $table->string('actual_value')->nullable(); // Nilai asli yang ditemukan di elemen
            $table->text('message')->nullable(); // Pesan, misal: "Nilai aktual 2.5, harusnya > 3"
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validation_results');
        Schema::dropIfExists('compliance_rules');
        Schema::dropIfExists('rule_sets');
    }
};