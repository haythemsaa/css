<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ticket listings table
        Schema::create('ticket_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('match_id')->constrained('matches')->onDelete('cascade');

            // Ticket details
            $table->string('ticket_number')->unique(); // Original ticket number for verification
            $table->enum('category', ['vip', 'tribune', 'populaire', 'virage'])->default('tribune');
            $table->string('section')->nullable();
            $table->string('row')->nullable();
            $table->string('seat_number')->nullable();
            $table->integer('quantity')->default(1);

            // Pricing
            $table->decimal('original_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('platform_fee_percentage', 5, 2)->default(5.00);

            // Status and verification
            $table->enum('status', ['pending_verification', 'available', 'reserved', 'sold', 'cancelled', 'rejected'])->default('pending_verification');
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');

            // Additional info
            $table->text('description')->nullable();
            $table->json('images')->nullable(); // Ticket photos for verification
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_negotiation')->default(false);
            $table->decimal('minimum_price', 10, 2)->nullable();

            // Reservation
            $table->foreignId('reserved_by')->nullable()->constrained('users');
            $table->timestamp('reserved_until')->nullable();

            // Timestamps
            $table->timestamp('listed_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('match_id');
            $table->index('category');
            $table->index('is_verified');
            $table->index('listed_at');
        });

        // Ticket transactions table
        Schema::create('ticket_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('ticket_listings')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');

            // Transaction details
            $table->string('transaction_number')->unique();
            $table->decimal('ticket_price', 10, 2);
            $table->decimal('platform_fee', 10, 2);
            $table->decimal('total_amount', 10, 2);

            // Payment
            $table->enum('payment_status', ['pending', 'processing', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Transfer status
            $table->enum('transfer_status', ['pending', 'transferred', 'failed'])->default('pending');
            $table->timestamp('transferred_at')->nullable();
            $table->text('transfer_notes')->nullable();

            // Meeting details (for physical ticket exchange)
            $table->enum('delivery_method', ['electronic', 'meeting', 'courier'])->default('electronic');
            $table->text('meeting_details')->nullable();
            $table->timestamp('meeting_scheduled_at')->nullable();

            // Review and rating
            $table->boolean('buyer_reviewed')->default(false);
            $table->boolean('seller_reviewed')->default(false);

            $table->timestamps();

            // Indexes
            $table->index('transaction_number');
            $table->index('payment_status');
            $table->index('transfer_status');
            $table->index('buyer_id');
            $table->index('seller_id');
        });

        // User reviews table (for marketplace trust)
        Schema::create('marketplace_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('ticket_transactions')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewed_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['buyer', 'seller']); // Role of the reviewer

            // Rating
            $table->integer('rating')->unsigned(); // 1-5 stars
            $table->text('comment')->nullable();
            $table->boolean('would_trade_again')->default(true);

            // Response
            $table->text('response')->nullable();
            $table->timestamp('responded_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('reviewed_user_id');
            $table->index('rating');
            $table->unique(['transaction_id', 'reviewer_id']);
        });

        // Ticket verification requests
        Schema::create('ticket_verification_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('ticket_listings')->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');

            // Verification details
            $table->enum('status', ['pending', 'in_review', 'approved', 'rejected'])->default('pending');
            $table->json('verification_documents')->nullable(); // Images, PDFs
            $table->text('notes')->nullable();

            // Review
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->text('review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('requested_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_verification_requests');
        Schema::dropIfExists('marketplace_reviews');
        Schema::dropIfExists('ticket_transactions');
        Schema::dropIfExists('ticket_listings');
    }
};
