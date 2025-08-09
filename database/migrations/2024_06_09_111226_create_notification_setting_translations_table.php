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
        Schema::create('notification_setting_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notification_setting_id');
            $table->unsignedBigInteger('language_id');
            $table->string('title');

            $table->unique(['notification_setting_id', 'language_id'], 'notification_setting_id_language_id_unique');
            $table->foreign('notification_setting_id', 'notification_setting_id_foreign')
                ->references('id')->on('notification_settings')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_setting_translations');
    }
};
