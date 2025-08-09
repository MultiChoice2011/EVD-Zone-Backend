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
        Schema::table('topup_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('order_product_id')->after('order_id');
            $table->foreign('order_product_id')->references('id')->on('order_products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('topup_transactions', function (Blueprint $table) {
            $table->dropForeign('topup_transactions_order_product_id_foreign');
            $table->dropColumn('order_product_id');
        });
    }
};
