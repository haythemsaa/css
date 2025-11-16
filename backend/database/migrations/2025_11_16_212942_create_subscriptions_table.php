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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Plan
            $table->enum('plan_type', ['monthly', 'annual'])->default('monthly');

            // Statut
            $table->enum('status', ['active', 'cancelled', 'expired'])->default('active');

            // Dates
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->boolean('auto_renew')->default(true);

            // Paiement
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('plan_type');
            $table->index('status');
            $table->index(['starts_at', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
