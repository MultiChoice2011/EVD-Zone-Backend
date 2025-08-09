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
        Schema::create('option_value_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_value_id')->constrained("option_values")->cascadeOnDelete();
            $table->foreignId('language_id')->constrained("languages")->cascadeOnDelete();
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('option_value_translations');
    }
};
