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
        Schema::create('integration_option_keys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('integration_id');
            $table->enum('key', ['account_id', 'phone', 'server_id']);
            $table->enum('type', ['option', 'option_value'])->default('option');
            $table->string('value');

            $table->foreign('integration_id')->references('id')->on('integrations')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('option_keys');
    }
};
