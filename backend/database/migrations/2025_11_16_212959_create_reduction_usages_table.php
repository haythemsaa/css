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
        Schema::create('reduction_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');
            $table->foreignId('offer_id')->nullable()->constrained('partner_offers')->onDelete('set null');
            $table->foreignId('code_id')->constrained('reduction_codes')->onDelete('cascade');

            // Utilisation
            $table->timestamp('used_at');
            $table->decimal('location_lat', 10, 8)->nullable();
            $table->decimal('location_lng', 11, 8)->nullable();
            $table->string('location_name')->nullable();

            // Montants
            $table->decimal('original_amount', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('final_amount', 10, 2)->nullable();

            // Paiement
            $table->string('payment_method')->nullable();
            $table->string('validation_method')->nullable();

            // Validation
            $table->foreignId('validated_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('validated_by_name')->nullable();

            // Transaction
            $table->string('transaction_reference')->nullable();
            $table->string('invoice_number')->nullable();

            // Commission
            $table->decimal('commission_earned', 10, 2)->default(0);
            $table->timestamp('commission_paid_at')->nullable();

            // Satisfaction
            $table->integer('user_satisfaction_rating')->nullable();
            $table->text('user_feedback')->nullable();

            // Statut et litiges
            $table->enum('status', ['validated', 'disputed', 'refunded', 'cancelled'])->default('validated');
            $table->text('dispute_reason')->nullable();
            $table->timestamp('dispute_resolved_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('partner_id');
            $table->index('offer_id');
            $table->index('code_id');
            $table->index('used_at');
            $table->index('status');
            $table->index(['location_lat', 'location_lng']);
            $table->index('validated_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reduction_usages');
    }
};
