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
        Schema::table('static_pages', function (Blueprint $table) {
            $table->string('key')->unique()->nullable()->after('id');
            $table->enum('status', ['active','inactive'])->default('active')->after('key');
            $table->timestamp('deleted_at')->nullable()->after('mobile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('static_pages', function (Blueprint $table) {
            $table->dropColumn(['status', 'mobile', 'deleted_at']);
        });
    }
};
