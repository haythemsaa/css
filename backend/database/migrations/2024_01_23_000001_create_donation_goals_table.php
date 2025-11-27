<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Donation Goals - Objectifs de dons avec causes spécifiques
        Schema::create('donation_goals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('full_details')->nullable();
            $table->string('category'); // litigation, player_transfer, stadium_renovation, youth_academy, equipment, debt_payment, other
            $table->decimal('target_amount', 12, 2);
            $table->decimal('current_amount', 12, 2)->default(0);
            $table->integer('donors_count')->default(0);
            $table->decimal('min_donation', 10, 2)->default(5); // Minimum 5 TND
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable(); // Null = pas de deadline
            $table->string('status')->default('active'); // active, paused, completed, cancelled
            $table->boolean('is_featured')->default(false);
            $table->string('featured_image')->nullable();
            $table->json('gallery_images')->nullable();
            $table->json('milestone_updates')->nullable(); // Updates sur la progression
            $table->string('impact_metrics')->nullable(); // Mesures d'impact (ex: "50% du stade rénové")
            $table->text('thank_you_message')->nullable();
            $table->boolean('show_donors')->default(true); // Afficher la liste des donateurs
            $table->boolean('allow_anonymous')->default(true);
            $table->json('rewards')->nullable(); // Récompenses par paliers (badges, recognition)
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index(['category', 'status']);
            $table->index('priority');
        });

        // Ajouter la colonne goal_id à la table donations existante
        Schema::table('donations', function (Blueprint $table) {
            $table->foreignId('goal_id')->nullable()->after('campaign_id')->constrained('donation_goals')->nullOnDelete();
            $table->boolean('is_anonymous')->default(false)->after('payment_status');
            $table->text('donor_message')->nullable()->after('is_anonymous');
            $table->string('dedication')->nullable()->after('donor_message'); // Dédicace (en mémoire de, pour célébrer, etc.)

            $table->index('goal_id');
        });

        // Donation Goal Milestones - Jalons de progression
        Schema::create('donation_goal_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained('donation_goals')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('target_amount', 12, 2);
            $table->integer('percentage')->default(0); // Ex: 25%, 50%, 75%, 100%
            $table->boolean('is_achieved')->default(false);
            $table->timestamp('achieved_at')->nullable();
            $table->string('reward_badge')->nullable(); // Badge spécial pour les donateurs
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index(['goal_id', 'percentage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_goal_milestones');

        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['goal_id']);
            $table->dropColumn(['goal_id', 'is_anonymous', 'donor_message', 'dedication']);
        });

        Schema::dropIfExists('donation_goals');
    }
};
