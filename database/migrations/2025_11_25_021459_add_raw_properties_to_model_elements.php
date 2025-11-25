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
        Schema::table('model_elements', function (Blueprint $table) {
            // Kolom JSON untuk menyimpan ribuan properti parameter dari Revit
            $table->json('raw_properties')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('model_elements', function (Blueprint $table) {
            //
        });
    }
};
