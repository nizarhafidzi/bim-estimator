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
        Schema::table('users', function (Blueprint $table) {
            $table->text('autodesk_access_token')->nullable();
            $table->text('autodesk_refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('autodesk_account_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'autodesk_access_token', 
                'autodesk_refresh_token', 
                'token_expires_at', 
                'autodesk_account_name'
            ]);
        });
    }
};
