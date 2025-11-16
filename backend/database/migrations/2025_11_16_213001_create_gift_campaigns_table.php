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
        Schema::create('gift_campaigns', function (Blueprint $table) {
            $table->id();

            // Campagne
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->nullable();

            // Type de cadeau
            $table->enum('gift_type', ['physical', 'digital', 'points', 'voucher'])->default('physical');

            // Déclencheur
            $table->enum('trigger_type', ['monthly', 'quarterly', 'annual', 'milestone', 'birthday', 'random'])->default('monthly');
            $table->json('trigger_config')->nullable();
            $table->json('eligibility_criteria')->nullable();

            // Critères
            $table->enum('membership_required', ['premium', 'socios', 'both', 'none'])->default('none');
            $table->integer('points_threshold')->nullable();
            $table->string('loyalty_level_required')->nullable();

            // Cadeaux
            $table->json('gift_items')->nullable();
            $table->decimal('budget_allocated', 10, 2)->default(0);
            $table->decimal('budget_used', 10, 2)->default(0);

            // Dates
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Statut
            $table->boolean('is_active')->default(true);
            $table->boolean('is_automated')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('type');
            $table->index('gift_type');
            $table->index('trigger_type');
            $table->index('is_active');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_campaigns');
    }
};
