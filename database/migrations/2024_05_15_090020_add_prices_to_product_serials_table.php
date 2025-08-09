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
        Schema::table('product_serials', function (Blueprint $table) {
            $table->decimal('price_before_vat',15,4)->default(0.0000)->after('expiring');
            $table->decimal('vat_amount',15,4)->default(0.0000)->after('price_before_vat');
            $table->decimal('price_after_vat',15,4)->default(0.0000)->after('vat_amount');
            $table->string('currency')->nullable()->after('price_after_vat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_serials', function (Blueprint $table) {
            $table->dropColumn(['price_before_vat', 'vat_amount', 'price_after_vat', 'currency']);
        });
    }
};
