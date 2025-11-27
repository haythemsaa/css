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
        // Match predictions table
        Schema::create('match_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('match_id')->constrained()->onDelete('cascade');
            $table->integer('predicted_home_score')->nullable();
            $table->integer('predicted_away_score')->nullable();
            $table->enum('predicted_result', ['home_win', 'draw', 'away_win'])->nullable();
            $table->foreignId('predicted_first_scorer_id')->nullable()->constrained('players')->onDelete('set null');
            $table->json('bonus_predictions')->nullable(); // yellow cards, corners, etc.
            $table->integer('points_earned')->default(0);
            $table->boolean('is_processed')->default(false);
            $table->timestamp('predicted_at');
            $table->timestamps();

            $table->unique(['user_id', 'match_id']);
            $table->index(['match_id', 'is_processed']);
        });

        // Prediction leaderboard table
        Schema::create('prediction_leaderboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('total_predictions')->default(0);
            $table->integer('correct_results')->default(0);
            $table->integer('correct_scores')->default(0);
            $table->integer('total_points')->default(0);
            $table->integer('current_streak')->default(0);
            $table->integer('best_streak')->default(0);
            $table->decimal('accuracy_percentage', 5, 2)->default(0);
            $table->integer('rank')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });

        // Fantasy leagues table
        Schema::create('fantasy_leagues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // join code
            $table->text('description')->nullable();
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['public', 'private'])->default('private');
            $table->integer('max_members')->default(20);
            $table->integer('budget')->default(100000000); // 100M budget
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        // Fantasy league members table
        Schema::create('fantasy_league_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained('fantasy_leagues')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('total_points')->default(0);
            $table->integer('rank')->nullable();
            $table->timestamp('joined_at');
            $table->timestamps();

            $table->unique(['league_id', 'user_id']);
        });

        // Fantasy teams table
        Schema::create('fantasy_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('league_id')->nullable()->constrained('fantasy_leagues')->onDelete('cascade');
            $table->string('name');
            $table->integer('budget_remaining')->default(100000000);
            $table->integer('total_points')->default(0);
            $table->integer('gameweek_points')->default(0);
            $table->integer('transfers_made')->default(0);
            $table->integer('transfers_available')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Fantasy team players table
        Schema::create('fantasy_team_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fantasy_team_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->enum('position', ['GK', 'DEF', 'MID', 'FWD']);
            $table->boolean('is_captain')->default(false);
            $table->boolean('is_vice_captain')->default(false);
            $table->boolean('is_starter')->default(true);
            $table->integer('purchase_price')->default(0);
            $table->integer('current_value')->default(0);
            $table->integer('points_earned')->default(0);
            $table->timestamp('added_at');
            $table->timestamps();

            $table->index(['fantasy_team_id', 'is_starter']);
        });

        // Fantasy gameweeks table
        Schema::create('fantasy_gameweeks', function (Blueprint $table) {
            $table->id();
            $table->integer('gameweek_number');
            $table->string('name'); // e.g., "Gameweek 1"
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->unique('gameweek_number');
        });

        // Fantasy gameweek scores table
        Schema::create('fantasy_gameweek_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fantasy_team_id')->constrained()->onDelete('cascade');
            $table->foreignId('gameweek_id')->constrained('fantasy_gameweeks')->onDelete('cascade');
            $table->integer('points')->default(0);
            $table->integer('transfers_made')->default(0);
            $table->integer('transfer_cost')->default(0);
            $table->integer('net_points')->default(0);
            $table->integer('rank')->nullable();
            $table->timestamps();

            $table->unique(['fantasy_team_id', 'gameweek_id']);
        });

        // Player fantasy statistics table
        Schema::create('player_fantasy_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('gameweek_id')->constrained('fantasy_gameweeks')->onDelete('cascade');
            $table->integer('goals')->default(0);
            $table->integer('assists')->default(0);
            $table->integer('clean_sheets')->default(0);
            $table->integer('yellow_cards')->default(0);
            $table->integer('red_cards')->default(0);
            $table->integer('minutes_played')->default(0);
            $table->integer('points')->default(0);
            $table->timestamps();

            $table->unique(['player_id', 'gameweek_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_fantasy_stats');
        Schema::dropIfExists('fantasy_gameweek_scores');
        Schema::dropIfExists('fantasy_gameweeks');
        Schema::dropIfExists('fantasy_team_players');
        Schema::dropIfExists('fantasy_teams');
        Schema::dropIfExists('fantasy_league_members');
        Schema::dropIfExists('fantasy_leagues');
        Schema::dropIfExists('prediction_leaderboards');
        Schema::dropIfExists('match_predictions');
    }
};
