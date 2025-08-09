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
        Schema::table('order_product_serials', function (Blueprint $table) {
            $table->unique(['product_serial_id'], 'order_product_serials_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_product_serials', function (Blueprint $table) {
            $table->dropUnique('order_product_serials_unique');
        });
    }
};
