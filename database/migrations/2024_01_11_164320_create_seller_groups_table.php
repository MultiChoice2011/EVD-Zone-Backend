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
        Schema::create('seller_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained("seller_groups")->cascadeOnUpdate()->nullOnDelete();
            $table->string('image')->default('images/no-image.png');
            $table->boolean('automatic')->default(0);
            $table->boolean('amount_sales')->nullable();
            $table->boolean('order_count')->nullable();
            $table->enum('status', ['active','inactive','archived'])->default('active');
            $table->integer('sort_order')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_groups');
    }
};
