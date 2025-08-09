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
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained("sellers")->nullOnDelete();
            $table->string('name');
            $table->string('owner_name')->unique();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('password');
            $table->enum('status', ['active','inactive'])->default('active');
            $table->enum('approval_status', ['pending','complete_profile','approved','rejected'])->default('pending');
            $table->text('logo')->nullable();
            $table->decimal('balance', 15, 4)->default(00.0000);
            $table->text('address_details')->nullable();
            $table->foreignId('seller_group_id')->nullable()->constrained("seller_groups")->nullOnDelete();
            $table->foreignId('seller_group_level_id')->nullable()->constrained("seller_group_levels")->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
