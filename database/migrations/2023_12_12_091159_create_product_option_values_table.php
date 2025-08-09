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
        Schema::create('product_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_option_id')->constrained("product_options")->cascadeOnDelete();
            $table->foreignId('product_id')->constrained("products")->cascadeOnDelete();
            $table->foreignId('option_id')->constrained("options")->cascadeOnDelete();
            $table->unsignedInteger('option_value_id')->nullable();
            $table->integer('quantity')->default('0');
            $table->tinyInteger('subtract')->default('1');
            $table->decimal('price',);
            $table->string('price_prefix')->default('+');
            $table->integer('points')->default('0');
            $table->string('points_prefix')->default('+');
            $table->decimal('weight',15,8);
            $table->string('weight_prefix')->default('+');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_option_values');
    }
};
