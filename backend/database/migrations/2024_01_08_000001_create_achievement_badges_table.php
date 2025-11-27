<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievement_badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon');
            $table->string('color')->nullable();

            // Badge type
            $table->enum('type', [
                'milestone',
                'activity',
                'loyalty',
                'engagement',
                'special'
            ])->default('milestone');

            // Requirements
            $table->json('requirements')->nullable(); // {type: 'matches_attended', count: 10}
            $table->integer('points_reward')->default(0);

            // Rarity
            $table->enum('rarity', ['common', 'rare', 'epic', 'legendary'])->default('common');

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_secret')->default(false); // Hidden until unlocked

            // Stats
            $table->integer('unlocked_by_count')->default(0);

            $table->timestamps();

            $table->index('slug');
            $table->index('type');
            $table->index('rarity');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievement_badges');
    }
};
