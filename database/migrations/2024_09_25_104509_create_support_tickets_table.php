<?php

use App\Enums\SupportTicketEnum;
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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->morphs('customer');
            $table->string('title');
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->longText('details')->nullable();
            $table->enum('status',[SupportTicketEnum::PENDING,SupportTicketEnum::INPROGRESS,SupportTicketEnum::CLOSE,
            SupportTicketEnum::COMPLETE])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
