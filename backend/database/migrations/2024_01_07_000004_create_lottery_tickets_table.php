<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lottery_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lottery_draw_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('ticket_number')->unique();
            $table->decimal('price_paid', 8, 2);

            // Payment
            $table->enum('payment_method', ['card', 'd17', 'konnect', 'points']);
            $table->string('transaction_id')->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'refunded'])->default('completed');

            // Winning
            $table->boolean('is_winner')->default(false);
            $table->boolean('prize_claimed')->default(false);
            $table->timestamp('prize_claimed_at')->nullable();

            $table->timestamps();

            $table->index('lottery_draw_id');
            $table->index('user_id');
            $table->index('ticket_number');
            $table->index('is_winner');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lottery_tickets');
    }
};
