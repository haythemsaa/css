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
        Schema::create('collectible_cards', function (Blueprint $table) {
            $table->id();

            // Carte
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            // Rareté
            $table->enum('rarity', ['common', 'rare', 'epic', 'legendary'])->default('common');

            // Catégorie
            $table->enum('category', ['player', 'historic', 'stadium'])->default('player');

            // Supply
            $table->integer('total_supply')->nullable();
            $table->date('release_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('rarity');
            $table->index('category');
            $table->index('release_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collectible_cards');
    }
};
