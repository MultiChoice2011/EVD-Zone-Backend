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
        Schema::create('cart_product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_option_id')->constrained()->onDelete('cascade');
            $table->foreignId('option_value_id')->nullable()->constrained('option_values')->onDelete('set null');
            $table->string('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_product_options');
    }
};
