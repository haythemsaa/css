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
        Schema::create('lottery_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('draw_id')->constrained('lottery_draws')->onDelete('cascade');

            // Ticket
            $table->string('ticket_number')->unique();
            $table->timestamp('purchase_date');
            $table->decimal('amount_paid', 8, 2);
            $table->boolean('is_winner')->default(false);

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('draw_id');
            $table->index('ticket_number');
            $table->index('is_winner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lottery_tickets');
    }
};
