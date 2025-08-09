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
        Schema::create('seller_group_custom_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('model_id');
            $table->foreignId('seller_group_id')->nullable()->constrained("seller_groups")->cascadeOnDelete();
            $table->enum('advantage', ['in','out'])->default('in');
            $table->enum('type', ['fixed','percentage'])->default('fixed');
            $table->enum('model_type', ['product','category'])->default('product');
            $table->decimal('amount',10,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_group_custom_prices');
    }
};
