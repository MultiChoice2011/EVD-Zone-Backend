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
        Schema::create('code_verifications', function (Blueprint $table) {
            $table->id();
            $table->morphs('verifiable');
            $table->string('code');
            $table->tinyInteger('is_id')->default(0);
            $table->enum('type', ['email','phone'])->default('phone');
            $table->string('token')->nullable();
            $table->tinyInteger('used')->default(0);
            $table->timestamp('expire_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_verifications');
    }
};
