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
        Schema::create('lottery_draws', function (Blueprint $table) {
            $table->id();

            // Tirage
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('prize_description')->nullable();

            // Tickets
            $table->decimal('ticket_price', 8, 2);
            $table->integer('total_tickets');
            $table->integer('tickets_sold')->default(0);

            // Tirage
            $table->dateTime('draw_date');
            $table->enum('status', ['upcoming', 'active', 'drawn', 'cancelled'])->default('upcoming');

            // Gagnant
            $table->foreignId('winner_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('winning_ticket_number')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('draw_date');
            $table->index('winner_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lottery_draws');
    }
};
