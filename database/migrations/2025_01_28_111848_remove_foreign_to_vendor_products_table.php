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
        Schema::dropIfExists('vendor_products');
        Schema::create('vendor_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained("vendors")->restrictOnDelete();
            $table->foreignId('product_id')->constrained("products")->restrictOnDelete();
            $table->foreignId('brand_id')->constrained("brands")->restrictOnDelete();
            $table->enum('type', ['serial', 'topup'])->default('serial');
            $table->bigInteger('vendor_product_id');
            $table->decimal('provider_cost', 15, 8)->default('0.0000');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_products', function (Blueprint $table) {
            //
        });
    }
};
