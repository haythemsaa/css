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
        Schema::create('leaderboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('leaderboard_type'); // 'points', 'donations', 'engagement', 'social'
            $table->integer('score')->default(0);
            $table->integer('rank')->default(0);
            $table->integer('previous_rank')->nullable();
            $table->string('period'); // 'daily', 'weekly', 'monthly', 'all_time'
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->json('metadata')->nullable(); // Additional stats
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();

            $table->index(['leaderboard_type', 'period', 'rank']);
            $table->index(['user_id', 'leaderboard_type']);
            $table->unique(['user_id', 'leaderboard_type', 'period', 'period_start']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaderboards');
    }
};
