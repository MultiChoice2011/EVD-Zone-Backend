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
        Schema::create('home_section_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('home_section_id');
            $table->unsignedBigInteger('language_id');
            $table->string('name');
            $table->string('title')->nullable();
            $table->tinyInteger('display')->default(1);
            $table->text('image')->nullable();
            $table->text('redirect_url')->nullable();
            $table->text('alt_name')->nullable();

            $table->unique(['home_section_id', 'language_id'], 'home_section_id_language_id_unique');
            $table->foreign('home_section_id', 'home_section_id_foreign_translations')->references('id')->on('home_sections')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_section_translations');
    }
};
