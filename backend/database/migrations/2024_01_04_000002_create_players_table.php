<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('slug')->unique();
            $table->integer('jersey_number')->nullable();
            $table->enum('position', [
                'goalkeeper',
                'defender',
                'midfielder',
                'forward'
            ]);
            $table->string('photo')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->integer('height')->nullable(); // cm
            $table->integer('weight')->nullable(); // kg
            $table->string('preferred_foot')->nullable();
            $table->decimal('market_value', 12, 2)->nullable(); // TND
            $table->text('biography')->nullable();

            // Career stats
            $table->integer('goals')->default(0);
            $table->integer('assists')->default(0);
            $table->integer('matches_played')->default(0);
            $table->integer('yellow_cards')->default(0);
            $table->integer('red_cards')->default(0);

            // Status
            $table->enum('status', ['active', 'injured', 'suspended', 'transferred'])->default('active');
            $table->date('injury_until')->nullable();
            $table->text('injury_description')->nullable();
            $table->date('suspension_until')->nullable();

            // Social media
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('position');
            $table->index('status');
            $table->index('jersey_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
