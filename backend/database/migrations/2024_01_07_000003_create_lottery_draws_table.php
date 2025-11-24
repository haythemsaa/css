<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lottery_draws', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            // Prize
            $table->string('prize_title');
            $table->text('prize_description')->nullable();
            $table->string('prize_image')->nullable();
            $table->decimal('prize_value', 10, 2)->nullable(); // TND

            // Tickets
            $table->decimal('ticket_price', 8, 2)->default(1.00); // TND
            $table->integer('max_tickets')->nullable();
            $table->integer('max_tickets_per_user')->default(10);
            $table->integer('tickets_sold')->default(0);

            // Eligibility
            $table->enum('eligible_users', ['all', 'premium', 'socios'])->default('all');

            // Draw
            $table->timestamp('draw_date');
            $table->boolean('is_drawn')->default(false);
            $table->timestamp('drawn_at')->nullable();
            $table->foreignId('winner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('winning_ticket_number')->nullable();

            // Status
            $table->enum('status', ['upcoming', 'active', 'drawn', 'completed', 'cancelled'])->default('upcoming');

            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('status');
            $table->index('draw_date');
            $table->index('is_drawn');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lottery_draws');
    }
};
