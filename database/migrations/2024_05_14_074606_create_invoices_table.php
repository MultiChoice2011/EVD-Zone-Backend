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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained("products")->restrictOnDelete()->restrictOnUpdate();
            $table->foreignId('vendor_id')->constrained("vendors")->restrictOnDelete()->restrictOnUpdate();
            $table->foreignId('user_id')->constrained("users")->restrictOnDelete()->restrictOnUpdate();
            $table->string('invoice_number')->nullable();
            $table->enum('type', ['manual', 'auto','file']);
            $table->decimal('price',15,4)->default(0.0000);
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
