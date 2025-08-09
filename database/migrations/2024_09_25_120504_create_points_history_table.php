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
        Schema::create('points_history', function (Blueprint $table) {
            $table->id();
            $table->morphs('pointable'); // `pointable_id` and `pointable_type`
            $table->integer('points');
            $table->enum('action_type', ['add', 'deduct'])->default('add');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points_history');
    }
};
