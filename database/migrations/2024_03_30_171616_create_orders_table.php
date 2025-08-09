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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->morphs('owner');
            $table->string('status');
            $table->string("payment_method")->default('cod');
            $table->double("total")->nullable();
            $table->double("sub_total")->nullable();
            $table->double("vat")->nullable();
            $table->double("tax")->nullable();
            $table->enum('order_source', ['web', 'mobile', 'dashboard'])->default('web');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('currency_id')->references('id')->on('currencies')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
