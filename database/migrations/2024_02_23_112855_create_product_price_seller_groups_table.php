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
        Schema::create('product_price_seller_groups', function (Blueprint $table) {
            //
            $table->id();
            $table->foreignId('product_id')->constrained("products")->cascadeOnDelete();
            $table->foreignId('seller_group_id')->nullable()->constrained("seller_groups")->cascadeOnDelete();
            $table->decimal('price',15,4)->default('0.0000');
            $table->decimal('amount_percentage',15,4)->default('0.0000');
            $table->integer('minimum_quantity');
            $table->integer('max_quantity');
            $table->integer('points_buy');
            $table->integer('points_sell');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_price_seller_groups');
    }
};
