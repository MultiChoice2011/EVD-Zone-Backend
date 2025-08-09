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
        Schema::create('bank_commission_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_commission_id')->constrained('bank_commissions')->cascadeOnDelete();
            $table->enum('name',['MADA','VISA','MASTER','STC_PAY','APPLEPAY']);
            $table->double('gate_fees')->default(0);
            $table->double('static_value')->default(0);
            $table->double('additional_value_fees')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_commission_settings');
    }
};
