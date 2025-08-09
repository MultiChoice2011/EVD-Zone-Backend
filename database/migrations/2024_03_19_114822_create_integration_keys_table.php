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
        Schema::create('integration_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')
                ->constrained("integrations")
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('key');
            $table->string('value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_keys');
    }
};
