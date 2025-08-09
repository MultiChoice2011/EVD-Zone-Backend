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
        Schema::create('slider_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('slider_id');
            $table->unsignedBigInteger('language_id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('image')->nullable();
            $table->text('redirect_url')->nullable();
            $table->text('alt_name')->nullable();

            $table->unique(['slider_id', 'language_id'], 'slider_id_language_id_unique');
            $table->foreign('slider_id', 'slider_id_foreign')->references('id')->on('sliders')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slider_translations');
    }
};
