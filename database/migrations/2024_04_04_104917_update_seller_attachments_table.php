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
        Schema::table('seller_attachments', function (Blueprint $table) {
            $table->string('extension')->nullable()->after('type');
            $table->string('size')->nullable()->after('extension');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_attachments', function (Blueprint $table) {
            $table->dropColumn(['extension', 'size', 'created_at', 'updated_at']);
        });
    }
};
