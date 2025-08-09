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
            $table->text('integration_order_id')->nullable()->after('transaction_id');
            $table->text('account_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('topup_transactions', function (Blueprint $table) {
            $table->dropColumn('integration_order_id');
        });
    }
};
