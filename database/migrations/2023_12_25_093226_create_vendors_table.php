<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained("countries")->nullOnDelete();
            $table->string('name');
            $table->string('logo')->default('images/no-logo.png');
            $table->enum('status', ['pending', 'approved', 'not_approved'])->default('pending');
            $table->text('street')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
