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
        Schema::create('players', function (Blueprint $table) {
            $table->id();

            // Nom
            $table->string('first_name');
            $table->string('last_name');
            $table->string('photo')->nullable();

            // Position
            $table->string('position');
            $table->integer('jersey_number')->nullable();
            $table->string('nationality')->nullable();

            // Détails personnels
            $table->date('birth_date')->nullable();
            $table->integer('height')->nullable(); // en cm
            $table->integer('weight')->nullable(); // en kg

            // Contrat
            $table->date('contract_expires_at')->nullable();
            $table->decimal('market_value', 12, 2)->nullable();

            // Biographie et stats
            $table->text('bio')->nullable();
            $table->json('statistics')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('position');
            $table->index('jersey_number');
            $table->index('nationality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
