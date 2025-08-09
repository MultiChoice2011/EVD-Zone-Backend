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
        Schema::table('integration_option_keys', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->foreign('parent_id')->references('id')->on('integration_option_keys');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integration_option_keys', function (Blueprint $table) {
            $table->dropForeign('integration_option_keys_parent_id_foreign');
            $table->dropColumn('parent_id');
        });
    }
};
