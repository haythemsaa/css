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
        // Table principale des sondages
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['single', 'multiple', 'rating', 'text'])->default('single');
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->enum('visibility', ['public', 'socios_only', 'admin_only'])->default('socios_only');
            $table->boolean('allow_anonymous')->default(false);
            $table->boolean('show_results_before_vote')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('max_votes_per_user')->default(1); // Pour type multiple
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('category')->nullable(); // match, transfer, club_decision, etc.
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'visibility']);
            $table->index('created_by');
            $table->index('category');
        });

        // Options de réponse pour les sondages
        Schema::create('poll_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->string('text');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index('poll_id');
        });

        // Votes des utilisateurs
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->foreignId('poll_option_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('text_response')->nullable(); // Pour questions ouvertes
            $table->integer('rating_value')->nullable(); // Pour notation 1-10
            $table->boolean('is_anonymous')->default(false);
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            // Un utilisateur ne peut voter qu'une fois par sondage (sauf anonymes)
            $table->unique(['poll_id', 'user_id'], 'unique_user_poll_vote');
            $table->index('poll_id');
            $table->index('user_id');
        });

        // Statistiques agrégées des sondages (cache)
        Schema::create('poll_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->integer('total_votes')->default(0);
            $table->integer('total_voters')->default(0);
            $table->json('option_results')->nullable(); // {option_id: count}
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            $table->unique('poll_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poll_statistics');
        Schema::dropIfExists('poll_votes');
        Schema::dropIfExists('poll_options');
        Schema::dropIfExists('polls');
    }
};
