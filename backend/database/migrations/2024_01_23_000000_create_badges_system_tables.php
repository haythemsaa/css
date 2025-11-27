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
        // Badges table - defines all available badges
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category'); // social, participation, achievement, special, loyalty, performance
            $table->string('icon')->nullable(); // icon identifier or emoji
            $table->string('image_url')->nullable(); // full badge image
            $table->enum('rarity', ['common', 'rare', 'epic', 'legendary'])->default('common');
            $table->integer('token_reward')->default(0); // tokens earned when unlocking
            $table->integer('xp_reward')->default(0); // XP earned when unlocking
            $table->boolean('is_secret')->default(false); // hidden until unlocked
            $table->boolean('is_active')->default(true);
            $table->json('unlock_criteria')->nullable(); // JSON with conditions
            $table->string('unlock_type'); // manual, auto, event, milestone, streak
            $table->integer('required_count')->default(1); // for progress-based badges
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        // User badges table - tracks earned badges
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->integer('progress')->default(0); // current progress towards unlock
            $table->integer('progress_max')->default(1); // required for unlock
            $table->boolean('is_unlocked')->default(false);
            $table->timestamp('unlocked_at')->nullable();
            $table->integer('tokens_earned')->default(0);
            $table->integer('xp_earned')->default(0);
            $table->json('metadata')->nullable(); // additional context
            $table->timestamps();

            $table->unique(['user_id', 'badge_id']);
            $table->index(['user_id', 'is_unlocked']);
        });

        // Badge statistics table - tracks overall stats
        Schema::create('badge_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->integer('total_unlocked')->default(0);
            $table->integer('total_in_progress')->default(0);
            $table->decimal('unlock_percentage', 5, 2)->default(0); // % of users who unlocked
            $table->timestamp('first_unlocked_at')->nullable();
            $table->timestamp('last_unlocked_at')->nullable();
            $table->timestamps();

            $table->unique('badge_id');
        });

        // User badge summary table - cached counts per user
        Schema::create('user_badge_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('total_badges')->default(0);
            $table->integer('common_badges')->default(0);
            $table->integer('rare_badges')->default(0);
            $table->integer('epic_badges')->default(0);
            $table->integer('legendary_badges')->default(0);
            $table->integer('total_tokens_from_badges')->default(0);
            $table->integer('total_xp_from_badges')->default(0);
            $table->decimal('completion_percentage', 5, 2)->default(0);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_badge_summaries');
        Schema::dropIfExists('badge_statistics');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
    }
};
