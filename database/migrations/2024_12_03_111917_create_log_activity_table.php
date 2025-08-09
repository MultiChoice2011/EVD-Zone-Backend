<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Get the migration connection name.
     */
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

        $schema->create('log_activity', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('guard')->nullable();
            $table->string('method');
            $table->string('auth_user_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('host');
            $table->ipAddress();
            $table->text('url')->nullable();
            $table->integer('status');
            $table->longText('exception')->nullable();
            $table->text('headers')->nullable();
            $table->text('payload')->nullable();
            $table->longText('response')->nullable();
            $table->string('model')->nullable();
            $table->string('model_id')->nullable();
            $table->string('status')->change();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_activity');
    }
};
