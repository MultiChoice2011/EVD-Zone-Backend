<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function getConnection(): ?string
    {
        return config('telescope.storage.database.connection');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $schema = Schema::connection($this->getConnection());
        $schema->create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('channel')->index();
            $table->string('level')->index();
            $table->text('message');
            $table->json('context')->nullable();
            $table->string('remote_addr')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $schema = Schema::connection($this->getConnection());
        $schema->dropIfExists('logs');
    }
};
