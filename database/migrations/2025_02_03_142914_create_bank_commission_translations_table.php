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
        Schema::create('bank_commission_translations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->foreignId('bank_commission_id')->constrained('bank_commissions')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_commission_translations');
    }
};
