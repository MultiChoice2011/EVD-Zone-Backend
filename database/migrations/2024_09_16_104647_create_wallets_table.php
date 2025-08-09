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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->enum('recharge_balance_type',['cash','visa']);
            $table->string('transferring_name');
            $table->string('receipt_image')->nullable();
            $table->text('notes')->nullable();
            $table->double('amount');
            $table->enum('type',['withdraw','add'])->nullable()->default('add');
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
