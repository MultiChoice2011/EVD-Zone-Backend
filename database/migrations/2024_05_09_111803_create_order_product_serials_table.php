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
        Schema::create('order_product_serials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('order_product_id');
            $table->foreignId('product_serial_id')->constrained("product_serials")->restrictOnDelete();
            $table->string("serial");
            $table->string("scratching");
            $table->date("buying");
            $table->date("expiring");

            $table->foreign('order_id')->references('id')->on('orders')->restrictOnDelete();
            $table->foreign('order_product_id')->references('id')->on('order_products')->restrictOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product_serials');
    }
};
