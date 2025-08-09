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
        Schema::table('vendors', function (Blueprint $table) {
            //
            $table->string('serial_number');
            $table->string('phone');
            $table->string('image_attach')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('owner_name');
            $table->boolean('web')->default(0);
            $table->boolean('mobile')->default(0);
            $table->foreignId('city_id')->nullable()->constrained("cities")->nullOnDelete();
            $table->foreignId('region_id')->nullable()->constrained("regions")->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            //
            $table->dropColumn('serial_number');
            $table->dropColumn('phone');
            $table->dropColumn('image_attach');
            $table->dropColumn('meta_keywords');
            $table->dropColumn('owner_name');
            $table->dropColumn('web');
            $table->dropColumn('mobile');
            $table->dropColumn('city_id');
            $table->dropColumn('region_id');

        });
    }
};
