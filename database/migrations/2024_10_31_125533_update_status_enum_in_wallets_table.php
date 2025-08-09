<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            DB::statement("ALTER TABLE wallets MODIFY COLUMN status ENUM('pending', 'complete', 'refused') NOT NULL DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            DB::statement("ALTER TABLE wallets MODIFY COLUMN status ENUM('pending', 'complete') NOT NULL DEFAULT 'pending'");
        });
    }
};
