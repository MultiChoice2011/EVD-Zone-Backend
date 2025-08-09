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
        Schema::create('home_section_brands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('home_section_id');
            $table->unsignedBigInteger('brand_id');

            $table->unique(['home_section_id', 'brand_id'], 'home_section_id_brand_id_unique');
            $table->foreign('home_section_id', 'home_section_id_foreign_brands')->references('id')->on('home_sections')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_section_brands');
    }
};
