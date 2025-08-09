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
        Schema::table('home_section_translations', function (Blueprint $table) {
            $table->dropForeign('home_section_translations_brand_id_foreign');
            $table->dropColumn('brand_id');
        });

        Schema::table('home_sections', function (Blueprint $table) {
            $table->unsignedBigInteger('brand_id')->nullable()->after('style');
            $table->foreign('brand_id')->references('id')->on('brands')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_sections', function (Blueprint $table) {
            //
        });
    }
};
