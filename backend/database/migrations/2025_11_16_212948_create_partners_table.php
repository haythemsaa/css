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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();

            // Informations de base
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('banner_image')->nullable();
            $table->foreignId('category_id')->constrained('partner_categories')->onDelete('cascade');
            $table->foreignId('subcategory_id')->nullable()->constrained('partner_categories')->onDelete('set null');

            // Description
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();

            // Réductions (SYSTÈME FREEOUI)
            $table->enum('reduction_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('reduction_value_premium', 8, 2)->default(0);
            $table->decimal('reduction_value_socios', 8, 2)->default(0);

            // Conditions
            $table->json('conditions')->nullable();
            $table->json('exclusions')->nullable();

            // Localisation
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('governorate')->nullable();
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Contact
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Horaires
            $table->json('opening_hours')->nullable();

            // Capacité
            $table->integer('capacity_daily')->nullable();
            $table->integer('capacity_weekly')->nullable();

            // Contrat
            $table->enum('status', ['active', 'inactive', 'pending', 'suspended'])->default('pending');
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->string('contract_document_url')->nullable();
            $table->decimal('commission_percentage', 5, 2)->default(0);

            // Options
            $table->boolean('is_online')->default(false);
            $table->boolean('is_geolocation_enabled')->default(false);
            $table->string('redemption_code_prefix', 10)->nullable();

            // Stats
            $table->integer('featured_order')->default(0);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('slug');
            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('status');
            $table->index('city');
            $table->index('governorate');
            $table->index(['latitude', 'longitude']);
            $table->index('featured_order');
            $table->index('rating_average');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
