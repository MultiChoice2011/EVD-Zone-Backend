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
            $table->dropColumn('status');
        });

        Schema::table('product_serials', function (Blueprint $table) {
            $table->enum('status', ['hold', 'free', 'presold', 'sold', 'refund', 'stopped', 'refused'])->default('free')->after('scratching');
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
