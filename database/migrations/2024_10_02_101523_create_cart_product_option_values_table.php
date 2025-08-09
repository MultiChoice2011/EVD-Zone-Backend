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
        Schema::create('cart_product_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_product_id')->constrained('cart_products')->cascadeOnDelete();
            $table->foreignId('cart_product_option_id')->constrained('cart_product_options')->cascadeOnDelete();
            $table->foreignId('option_value_id')->constrained('option_values')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_product_option_values');
    }
};
