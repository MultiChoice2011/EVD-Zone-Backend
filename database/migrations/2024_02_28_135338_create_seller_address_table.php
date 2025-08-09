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
        Schema::create('seller_address', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained("sellers")->cascadeOnDelete();
            $table->foreignId('country_id')->nullable()->constrained("countries")->restrictOnDelete();
            $table->foreignId('city_id')->nullable()->constrained("cities")->restrictOnDelete();
            $table->foreignId('region_id')->nullable()->constrained("regions")->restrictOnDelete();
            $table->text('street')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_address');
    }
};
