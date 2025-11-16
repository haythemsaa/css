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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();

            // Match
            $table->string('opponent');
            $table->string('competition');
            $table->string('stadium')->nullable();

            // Date et heure
            $table->date('match_date');
            $table->time('kick_off_time');

            // Localisation
            $table->enum('home_away', ['home', 'away'])->default('home');

            // Score
            $table->integer('css_score')->nullable();
            $table->integer('opponent_score')->nullable();

            // Statut
            $table->enum('status', ['scheduled', 'live', 'finished'])->default('scheduled');

            // Détails
            $table->integer('attendance')->nullable();
            $table->string('referee')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('match_date');
            $table->index('status');
            $table->index('competition');
            $table->index('home_away');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
