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
        Schema::table('projects', function (Blueprint $table) {
            // Cek dulu biar gak error kalau kolom sudah ada
            if (!Schema::hasColumn('projects', 'error_message')) {
                $table->text('error_message')->nullable();
            }
            if (!Schema::hasColumn('projects', 'debug_logs')) {
                $table->json('debug_logs')->nullable(); // Kolom JSON untuk simpan array log
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            //
        });
    }
};
