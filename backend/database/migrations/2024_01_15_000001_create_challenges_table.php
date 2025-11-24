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
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('challenge_type'); // 'daily', 'weekly', 'monthly', 'special', 'seasonal'
            $table->string('category'); // 'engagement', 'social', 'donation', 'content', 'match'
            $table->string('difficulty'); // 'easy', 'medium', 'hard', 'expert'
            $table->json('requirements'); // What needs to be completed
            $table->integer('target_value'); // How many/much is needed
            $table->integer('points_reward')->default(0);
            $table->json('additional_rewards')->nullable(); // Badges, items, etc.
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->integer('max_completions')->default(1); // How many times can be completed
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('completion_count')->default(0);
            $table->timestamps();

            $table->index(['challenge_type', 'is_active']);
            $table->index(['category', 'difficulty']);
            $table->index(['starts_at', 'ends_at']);
        });

        Schema::create('user_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
            $table->integer('current_progress')->default(0);
            $table->integer('target_value');
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->boolean('reward_claimed')->default(false);
            $table->json('progress_data')->nullable(); // Additional tracking data
            $table->timestamps();

            $table->index(['user_id', 'is_completed']);
            $table->index(['challenge_id', 'completed_at']);
            $table->unique(['user_id', 'challenge_id', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_challenges');
        Schema::dropIfExists('challenges');
    }
};
