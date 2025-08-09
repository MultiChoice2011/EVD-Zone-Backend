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
        Schema::table('products', function (Blueprint $table) {
            //
            $table->string('sku');
            $table->integer('notify');
            $table->integer('minimum_quantity');
            $table->integer('max_quantity');
            $table->decimal('wholesale_price',15,4)->default('0.0000');
            $table->unsignedBigInteger('tax_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
