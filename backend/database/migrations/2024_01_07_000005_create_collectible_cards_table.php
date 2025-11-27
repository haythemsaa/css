<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collectible_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->nullable()->constrained()->onDelete('set null');

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image');
            $table->string('card_number')->unique();

            // Card attributes
            $table->enum('rarity', ['common', 'rare', 'epic', 'legendary'])->default('common');
            $table->string('season');
            $table->enum('card_type', ['player', 'historic', 'special'])->default('player');

            // Stats (for player cards)
            $table->integer('overall_rating')->nullable();
            $table->integer('attack_rating')->nullable();
            $table->integer('defense_rating')->nullable();
            $table->integer('speed_rating')->nullable();

            // Availability
            $table->integer('total_supply')->nullable(); // null = unlimited
            $table->integer('minted_count')->default(0);
            $table->boolean('is_tradeable')->default(true);

            // Acquisition
            $table->enum('acquisition_method', [
                'purchase',
                'lottery',
                'reward',
                'gift',
                'trade'
            ])->default('purchase');
            $table->integer('points_cost')->nullable();
            $table->decimal('price', 8, 2)->nullable(); // TND

            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('rarity');
            $table->index('season');
            $table->index('card_type');
            $table->index('is_tradeable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collectible_cards');
    }
};
