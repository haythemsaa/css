<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // User token wallets
        Schema::create('fan_token_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('lifetime_earned', 15, 2)->default(0);
            $table->decimal('lifetime_spent', 15, 2)->default(0);
            $table->integer('level')->default(1); // Loyalty level
            $table->integer('experience_points')->default(0);
            $table->timestamp('last_daily_bonus_at')->nullable();
            $table->timestamps();

            $table->index('balance');
            $table->index('level');
        });

        // Token transactions
        Schema::create('fan_token_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('transaction_number')->unique();

            // Transaction details
            $table->enum('type', ['earn', 'spend', 'transfer_in', 'transfer_out', 'bonus', 'refund'])->default('earn');
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);

            // Source of transaction
            $table->string('source_type')->nullable(); // match_attendance, purchase, poll_vote, etc.
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('description')->nullable();

            // Transfer details (if type is transfer)
            $table->foreignId('from_user_id')->nullable()->constrained('users');
            $table->foreignId('to_user_id')->nullable()->constrained('users');

            // Reward redemption (if type is spend)
            $table->foreignId('reward_redemption_id')->nullable()->constrained('reward_redemptions');

            $table->json('metadata')->nullable(); // Additional data
            $table->timestamps();

            $table->index('transaction_number');
            $table->index('type');
            $table->index('user_id');
            $table->index(['source_type', 'source_id']);
        });

        // Rewards catalog
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category'); // merchandise, experience, discount, digital, exclusive

            // Pricing
            $table->integer('token_cost');
            $table->decimal('monetary_value', 10, 2)->nullable(); // Real world value

            // Availability
            $table->integer('stock_quantity')->nullable(); // null = unlimited
            $table->integer('stock_remaining')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('minimum_level')->default(1); // Required user level

            // Details
            $table->json('images')->nullable();
            $table->string('terms_and_conditions')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();

            // Limits
            $table->integer('max_per_user')->nullable(); // Max redemptions per user
            $table->integer('max_per_day')->nullable();

            // Stats
            $table->integer('total_redeemed')->default(0);
            $table->integer('popularity_score')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('category');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('token_cost');
        });

        // Reward redemptions
        Schema::create('reward_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reward_id')->constrained('rewards')->onDelete('cascade');
            $table->string('redemption_code')->unique();

            // Transaction
            $table->integer('tokens_spent');
            $table->foreignId('transaction_id')->nullable()->constrained('fan_token_transactions');

            // Status
            $table->enum('status', ['pending', 'processing', 'fulfilled', 'cancelled', 'expired'])->default('pending');
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            // Fulfillment
            $table->text('fulfillment_notes')->nullable();
            $table->foreignId('fulfilled_by')->nullable()->constrained('users');

            // Delivery (for physical rewards)
            $table->string('delivery_method')->nullable(); // pickup, shipping, digital
            $table->text('delivery_address')->nullable();
            $table->string('tracking_number')->nullable();

            $table->timestamps();

            $table->index('redemption_code');
            $table->index('status');
            $table->index('user_id');
            $table->index('reward_id');
        });

        // Token earning activities configuration
        Schema::create('token_earning_activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity_type')->unique(); // match_attendance, purchase, poll_vote, etc.
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('tokens_awarded');
            $table->boolean('is_active')->default(true);
            $table->integer('daily_limit')->nullable(); // Max times per day
            $table->integer('total_limit')->nullable(); // Max times ever
            $table->json('conditions')->nullable(); // Additional conditions
            $table->timestamps();

            $table->index('activity_type');
            $table->index('is_active');
        });

        // User activity tracking (for earning limits)
        Schema::create('user_token_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('activity_type');
            $table->integer('tokens_earned');
            $table->date('activity_date');
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'activity_type', 'activity_date']);
            $table->index(['source_type', 'source_id']);
        });

        // Leaderboard (monthly reset)
        Schema::create('token_leaderboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('year');
            $table->integer('month');
            $table->decimal('tokens_earned', 15, 2)->default(0);
            $table->integer('rank')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'year', 'month']);
            $table->index(['year', 'month', 'tokens_earned']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_leaderboards');
        Schema::dropIfExists('user_token_activities');
        Schema::dropIfExists('token_earning_activities');
        Schema::dropIfExists('reward_redemptions');
        Schema::dropIfExists('rewards');
        Schema::dropIfExists('fan_token_transactions');
        Schema::dropIfExists('fan_token_wallets');
    }
};
