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
        Schema::table('order_products', function (Blueprint $table) {
            $table->double('cost_price')->default(0)->after('unit_price');
            $table->double('total_cost')->default(0)->after('total');
            $table->double('profit')->default(0)->after('total_cost');
            $table->double('nominal_price')->default(0)->after('profit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'total_cost', 'profit', 'nominal_price', 'profit']);
        });
    }
};
