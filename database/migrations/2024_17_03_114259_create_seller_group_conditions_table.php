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
        Schema::create('seller_group_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_group_id')->constrained("seller_groups")->cascadeOnDelete();
            $table->enum('condition_type', ['orders_number', 'order_amount', 'created_at', 'region_id']);
            $table->enum('comparison_operator', ['greater_than', 'less_than', 'between']);
            $table->string('value');
            $table->string('value2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_group_conditions');
    }
};
