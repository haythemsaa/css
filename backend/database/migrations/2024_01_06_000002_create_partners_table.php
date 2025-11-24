<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->constrained('partner_categories')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();

            // Contact info
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Location
            $table->string('address');
            $table->string('city');
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Reductions
            $table->decimal('reduction_value_premium', 5, 2); // % for Premium users
            $table->decimal('reduction_value_socios', 5, 2); // % for Socios
            $table->enum('reduction_type', ['percentage', 'fixed'])->default('percentage');

            // Business hours
            $table->json('opening_hours')->nullable();
            $table->text('special_conditions')->nullable();

            // Commission
            $table->decimal('commission_percentage', 5, 2)->default(5.00); // % to CSS club

            // Limits
            $table->integer('capacity_daily')->nullable(); // max uses per day
            $table->integer('capacity_per_user')->nullable(); // max uses per user
            $table->decimal('max_discount_amount', 10, 2)->nullable(); // TND

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('priority')->default(0);

            // Contract
            $table->date('contract_starts_at')->nullable();
            $table->date('contract_ends_at')->nullable();

            // Stats
            $table->integer('total_uses')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0); // TND saved by users
            $table->decimal('total_commission', 12, 2)->default(0); // TND earned by CSS
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('category_id');
            $table->index('city');
            $table->index(['latitude', 'longitude']);
            $table->index('is_active');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
