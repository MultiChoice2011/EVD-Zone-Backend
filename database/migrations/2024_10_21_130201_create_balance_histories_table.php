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
        Schema::create('balance_histories', function (Blueprint $table) {
            $table->id();
            $table->morphs('ownerble'); // Polymorphic fields (ownerble_id, ownerble_type)
            $table->unsignedBigInteger('order_id');
            $table->decimal('balance_before', 15, 4);
            $table->decimal('balance_after', 15, 4);
            $table->timestamps();

            // Foreign key to orders
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_histories');
    }
};
