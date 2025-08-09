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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained("brands")->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained("categories")->nullOnDelete();
            $table->string('serial');
            $table->integer('quantity')->default('0');
            $table->string('image')->default('no-image.png');
            $table->decimal('price',15,4)->default('0.0000');
            $table->decimal('cost_price',15,4)->default('0.0000');
            $table->integer('points')->default('0');
            $table->enum('status', ['active','inactive','pending'])->default('pending');
            $table->integer('sort_order')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
