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
        Schema::create('partner_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');

            // Informations de base
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();

            // Type d'offre
            $table->enum('offer_type', ['standard', 'flash', 'seasonal', 'exclusive'])->default('standard');

            // Réduction
            $table->decimal('reduction_value', 8, 2);
            $table->enum('reduction_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('min_purchase_amount', 10, 2)->nullable();
            $table->decimal('max_discount_amount', 10, 2)->nullable();

            // Validité
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();

            // Planification
            $table->json('days_of_week')->nullable(); // ["monday", "tuesday", ...]
            $table->json('time_slots')->nullable(); // [{"start": "10:00", "end": "14:00"}]

            // Stock
            $table->integer('stock_available')->nullable();
            $table->integer('stock_used')->default(0);

            // Limites utilisateur
            $table->integer('user_limit_per_day')->nullable();
            $table->integer('user_limit_per_month')->nullable();

            // Membership requis
            $table->enum('membership_required', ['premium', 'socios', 'both'])->default('both');

            // Conditions
            $table->text('terms_and_conditions')->nullable();

            // Images
            $table->string('image_url')->nullable();
            $table->json('images')->nullable();

            // Mise en avant
            $table->boolean('is_featured')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamp('notification_sent_at')->nullable();

            // Statut
            $table->enum('status', ['active', 'expired', 'coming_soon', 'draft'])->default('draft');

            // Statistiques
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('clicks_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('partner_id');
            $table->index('slug');
            $table->index('offer_type');
            $table->index('status');
            $table->index(['valid_from', 'valid_until']);
            $table->index('is_featured');
            $table->index('membership_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_offers');
    }
};
