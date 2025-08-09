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
            $table->enum('status', ['hold', 'free', 'presold', 'sold', 'refund', 'stopped'])->default('free')->after('scratching');
            $table->timestamp('hold_time')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_serials', function (Blueprint $table) {
            //
        });
    }
};
