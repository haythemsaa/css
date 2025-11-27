<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // User Activity Analytics
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('activity_type'); // 'content_view', 'product_view', 'purchase', 'donation', etc.
            $table->string('entity_type')->nullable(); // 'Content', 'Product', 'Match', etc.
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('metadata')->nullable(); // Additional data
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_type')->nullable(); // 'mobile', 'tablet', 'desktop'
            $table->string('platform')->nullable(); // 'ios', 'android', 'web'
            $table->integer('duration')->nullable(); // Duration in seconds
            $table->timestamps();

            $table->index(['user_id', 'activity_type']);
            $table->index(['entity_type', 'entity_id']);
            $table->index(['created_at']);
        });

        // Content Analytics
        Schema::create('content_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('views')->default(0);
            $table->integer('unique_views')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('shares')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('avg_read_time')->default(0); // Average in seconds
            $table->decimal('engagement_rate', 5, 2)->default(0); // Percentage
            $table->timestamps();

            $table->unique(['content_id', 'date']);
            $table->index(['date']);
        });

        // E-commerce Analytics
        Schema::create('ecommerce_analytics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('product_views')->default(0);
            $table->integer('cart_additions')->default(0);
            $table->integer('cart_abandonments')->default(0);
            $table->integer('orders_created')->default(0);
            $table->integer('orders_completed')->default(0);
            $table->decimal('total_revenue', 10, 2)->default(0);
            $table->decimal('avg_order_value', 10, 2)->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['date']);
        });

        // User Engagement Metrics
        Schema::create('user_engagement_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('sessions_count')->default(0);
            $table->integer('total_time_spent')->default(0); // in seconds
            $table->integer('pages_viewed')->default(0);
            $table->integer('actions_taken')->default(0);
            $table->integer('content_consumed')->default(0);
            $table->integer('social_interactions')->default(0);
            $table->integer('purchases_made')->default(0);
            $table->decimal('engagement_score', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'date']);
            $table->index(['date']);
        });

        // Daily Streak Tracking
        Schema::create('user_streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->integer('total_active_days')->default(0);
            $table->timestamps();

            $table->unique(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_streaks');
        Schema::dropIfExists('user_engagement_metrics');
        Schema::dropIfExists('ecommerce_analytics');
        Schema::dropIfExists('content_analytics');
        Schema::dropIfExists('user_activities');
    }
};
