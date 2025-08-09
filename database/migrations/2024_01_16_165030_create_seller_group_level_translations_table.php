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
        Schema::create('seller_group_level_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_group_level_id')->nullable()->constrained("seller_group_levels")->cascadeOnDelete();
            $table->foreignId('language_id')->constrained("languages")->cascadeOnDelete();
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_group_level_translations');
    }
};
