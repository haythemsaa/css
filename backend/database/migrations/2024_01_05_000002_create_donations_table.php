<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('campaign_id')->nullable()->constrained()->onDelete('set null');

            $table->decimal('amount', 10, 2); // TND
            $table->string('currency')->default('TND');

            // Payment info
            $table->enum('payment_method', [
                'card',
                'd17',
                'konnect',
                'paymee',
                'sadad',
                'bank_transfer'
            ]);
            $table->string('transaction_id')->unique()->nullable();
            $table->string('payment_gateway')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');

            // Donor info (if anonymous)
            $table->boolean('is_anonymous')->default(false);
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();

            // Recurring
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurring_interval', ['monthly', 'quarterly', 'yearly'])->nullable();
            $table->timestamp('next_donation_at')->nullable();

            // Certificate
            $table->string('certificate_number')->unique()->nullable();
            $table->string('certificate_pdf')->nullable();

            // Message
            $table->text('message')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('payment_method');
            $table->index('is_recurring');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
