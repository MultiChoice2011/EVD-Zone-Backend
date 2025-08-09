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
        Schema::table('seller_group_conditions', function (Blueprint $table) {
            $table->dropColumn('comparison_operator');
        });

        Schema::table('seller_group_conditions', function (Blueprint $table) {
            $table->enum('comparison_operator', ['greater_than', 'less_than', 'between'])->nullable()->after('condition_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_group_conditions', function (Blueprint $table) {
            //
        });
    }
};
