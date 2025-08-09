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
        Schema::create('brand_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_id');
            $table->enum('key', ['logo', 'h_image', 'v_image']);
            $table->text('image')->nullable();

            $table->unique(['brand_id', 'key']);
            $table->foreign('brand_id')->references('id')->on('brands')->cascadeOnDelete()->cascadeOnUpdate();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_images');
    }
};
