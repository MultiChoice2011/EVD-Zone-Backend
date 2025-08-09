<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            DB::statement("ALTER TABLE wallets MODIFY COLUMN recharge_balance_type ENUM('cash', 'visa', 'mada', 'add-by-admin') NOT NULL");
            // Add status column with enum values and default to 'pending'
            DB::statement("ALTER TABLE wallets ADD COLUMN status ENUM('pending', 'complete') NOT NULL DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn('recharge_balance_type');
            $table->dropColumn('status');
        });
    }
};
