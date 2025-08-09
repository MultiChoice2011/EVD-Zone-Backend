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
        Schema::create('static_page_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('static_page_id')->constrained("static_pages")->cascadeOnDelete();
            $table->foreignId('language_id')->constrained("languages")->cascadeOnDelete();
            $table->string('title');
            $table->string('meta_title');
            $table->string('meta_description');
            $table->string('meta_keywords');
            $table->text('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('static_page_translations');
    }
};
