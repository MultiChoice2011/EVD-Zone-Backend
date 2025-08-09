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
        Schema::table('seller_group_conditions', function (Blueprint $table) {
            $table->dropColumn('condition_type');
        });

        Schema::table('seller_group_conditions', function (Blueprint $table) {
            $table->enum('condition_type', ['orders_number', 'orders_amount', 'created_at', 'region_id'])->after('seller_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_group_conditions', function (Blueprint $table) {
            //
        });
    }
};
