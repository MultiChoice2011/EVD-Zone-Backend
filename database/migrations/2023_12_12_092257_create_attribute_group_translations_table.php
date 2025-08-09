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
        Schema::create('attribute_group_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_group_id')->constrained("attribute_groups")->cascadeOnDelete();
            $table->foreignId('language_id')->constrained("languages")->cascadeOnDelete();
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_group_translations');
    }
};
