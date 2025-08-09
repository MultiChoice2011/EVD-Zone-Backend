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
        Schema::create('seller_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained("sellers")->cascadeOnDelete();
            $table->text('file_url');
            $table->enum('type', ['identity','commercial_register','tax_card','more']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_attachments');
    }
};
