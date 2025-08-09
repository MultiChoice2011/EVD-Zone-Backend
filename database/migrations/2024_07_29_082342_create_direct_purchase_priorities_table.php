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
        Schema::create('direct_purchase_priorities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('direct_purchase_id');
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->integer('priority_level');

            $table->foreign('direct_purchase_id')->references('id')->on('direct_purchases')->restrictOnDelete();
            $table->foreign('vendor_id')->references('id')->on('vendors')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direct_purchase_priorities');
    }
};
