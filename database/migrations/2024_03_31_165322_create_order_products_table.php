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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained("orders")->cascadeOnDelete();
            $table->foreignId('product_id')->constrained("products")->restrictOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained("brands")->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained("vendors")->cascadeOnDelete();
            $table->enum('type', ['serial', 'topup', 'gift'])->default('serial');
            $table->enum('status', ['waiting', 'in_progress', 'completed', 'rejected'])->default('waiting');
            $table->double('total');
            $table->double('quantity');
            $table->double('unit_price');
            $table->decimal('tax_value',15,4)->default(0.0000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
