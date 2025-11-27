<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Subscription Tiers
        Schema::create('subscription_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 'Free', 'Bronze', 'Silver', 'Gold', 'Platinum'
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('monthly_price', 10, 2);
            $table->decimal('yearly_price', 10, 2);
            $table->json('features'); // List of features
            $table->json('benefits'); // List of benefits
            $table->string('badge_color')->nullable();
            $table->string('badge_icon')->nullable();
            $table->integer('points_multiplier')->default(1); // 1x, 2x, 3x points
            $table->integer('discount_percentage')->default(0); // E-commerce discount
            $table->boolean('priority_support')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'display_order']);
        });

        // Enhanced Subscriptions
        Schema::create('subscriptions_enhanced', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tier_id')->constrained('subscription_tiers')->onDelete('cascade');
            $table->string('billing_cycle'); // 'monthly', 'yearly'
            $table->decimal('amount', 10, 2);
            $table->string('status'); // 'active', 'cancelled', 'expired', 'suspended'
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->timestamp('next_billing_date')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->string('payment_method')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->string('gateway_subscription_id')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['expires_at']);
        });

        // Discount Codes / Coupons
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('discount_type'); // 'percentage', 'fixed_amount', 'free_shipping'
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_purchase_amount', 10, 2)->nullable();
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable(); // Total usage limit
            $table->integer('usage_limit_per_user')->default(1);
            $table->integer('usage_count')->default(0);
            $table->timestamp('valid_from');
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('applicable_categories')->nullable(); // Product categories
            $table->json('applicable_products')->nullable(); // Specific products
            $table->string('user_type')->nullable(); // 'all', 'new', 'socios', etc.
            $table->timestamps();

            $table->index(['code', 'is_active']);
            $table->index(['valid_from', 'valid_until']);
        });

        // Discount Code Usage
        Schema::create('discount_code_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_code_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('discount_amount', 10, 2);
            $table->timestamps();

            $table->index(['discount_code_id', 'user_id']);
        });

        // Product Bundles
        Schema::create('product_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('original_price', 10, 2);
            $table->decimal('bundle_price', 10, 2);
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('images')->nullable();
            $table->timestamps();

            $table->index(['is_available', 'is_featured']);
        });

        // Bundle Products
        Schema::create('bundle_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained('product_bundles')->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->index(['bundle_id']);
        });

        // Abandoned Carts Tracking
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total_value', 10, 2);
            $table->integer('items_count');
            $table->timestamp('abandoned_at');
            $table->boolean('recovery_email_sent')->default(false);
            $table->timestamp('recovery_email_sent_at')->nullable();
            $table->boolean('recovered')->default(false);
            $table->foreignId('recovery_order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->timestamps();

            $table->index(['user_id', 'recovered']);
            $table->index(['abandoned_at']);
        });

        // Pre-Orders
        Schema::create('pre_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->string('status')->default('pending'); // 'pending', 'confirmed', 'fulfilled', 'cancelled'
            $table->timestamp('expected_availability_date')->nullable();
            $table->boolean('notified')->default(false);
            $table->timestamps();

            $table->index(['product_id', 'status']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_orders');
        Schema::dropIfExists('abandoned_carts');
        Schema::dropIfExists('bundle_products');
        Schema::dropIfExists('product_bundles');
        Schema::dropIfExists('discount_code_usage');
        Schema::dropIfExists('discount_codes');
        Schema::dropIfExists('subscriptions_enhanced');
        Schema::dropIfExists('subscription_tiers');
    }
};
