<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('away_team_id')->constrained('teams')->onDelete('cascade');
            $table->string('competition'); // Ligue 1, CAF Champions League, etc.
            $table->string('season'); // 2024/2025
            $table->integer('round')->nullable();
            $table->string('venue')->nullable();
            $table->timestamp('match_date');

            // Score
            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();
            $table->integer('home_halftime_score')->nullable();
            $table->integer('away_halftime_score')->nullable();

            // Status
            $table->enum('status', [
                'scheduled',
                'live',
                'halftime',
                'finished',
                'postponed',
                'cancelled'
            ])->default('scheduled');

            // Live stats
            $table->integer('home_possession')->nullable(); // %
            $table->integer('away_possession')->nullable();
            $table->integer('home_shots')->nullable();
            $table->integer('away_shots')->nullable();
            $table->integer('home_shots_on_target')->nullable();
            $table->integer('away_shots_on_target')->nullable();
            $table->integer('home_corners')->nullable();
            $table->integer('away_corners')->nullable();
            $table->integer('home_fouls')->nullable();
            $table->integer('away_fouls')->nullable();
            $table->integer('home_yellow_cards')->nullable();
            $table->integer('away_yellow_cards')->nullable();
            $table->integer('home_red_cards')->nullable();
            $table->integer('away_red_cards')->nullable();

            // Match info
            $table->string('referee')->nullable();
            $table->integer('attendance')->nullable();
            $table->text('match_report')->nullable();
            $table->string('video_highlights_url')->nullable();

            // Lineups
            $table->json('home_lineup')->nullable();
            $table->json('away_lineup')->nullable();
            $table->json('home_substitutes')->nullable();
            $table->json('away_substitutes')->nullable();

            // Events (goals, cards, substitutions)
            $table->json('match_events')->nullable();

            // Broadcast
            $table->string('tv_channel')->nullable();
            $table->string('live_stream_url')->nullable();
            $table->boolean('has_live_commentary')->default(false);

            $table->timestamps();

            $table->index('match_date');
            $table->index('status');
            $table->index('competition');
            $table->index('season');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
