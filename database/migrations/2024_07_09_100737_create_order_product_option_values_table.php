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
        Schema::table('order_product_options', function (Blueprint $table) {
            $table->dropForeign('order_product_options_option_value_id_foreign');
            $table->dropColumn('option_value_id');
        });

        Schema::create('order_product_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_product_id')->constrained('order_products')->cascadeOnDelete();
            $table->foreignId('order_product_option_id')->constrained('order_product_options')->cascadeOnDelete();
            $table->foreignId('option_value_id')->constrained('option_values')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product_option_values');
    }
};
