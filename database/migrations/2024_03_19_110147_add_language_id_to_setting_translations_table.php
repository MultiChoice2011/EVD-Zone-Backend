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
        Schema::dropIfExists('setting_translations');


        Schema::create('setting_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('setting_id');
            $table->unsignedBigInteger('language_id');
            $table->longText('value')->nullable();

            $table->unique(['setting_id', 'language_id']);
            $table->foreign('setting_id')->references('id')->on('settings')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_translations');
    }
};
