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
        Schema::table('vendor_products', function (Blueprint $table) {
            $table->decimal('provider_cost', 15, 4)->default('0.0000')->after('vendor_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_products', function (Blueprint $table) {
            $table->dropColumn('provider_cost');
        });
    }
};
