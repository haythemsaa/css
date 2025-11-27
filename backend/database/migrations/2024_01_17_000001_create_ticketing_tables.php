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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->onDelete('cascade');
            $table->string('category'); // 'vip', 'tribune', 'pelouse', 'family'
            $table->string('section');
            $table->decimal('price', 10, 2);
            $table->integer('total_quantity');
            $table->integer('available_quantity');
            $table->integer('sold_quantity')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamp('sale_starts_at')->nullable();
            $table->timestamp('sale_ends_at')->nullable();
            $table->json('benefits')->nullable(); // Special benefits for this ticket type
            $table->timestamps();

            $table->index(['match_id', 'category', 'is_available']);
        });

        Schema::create('ticket_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('match_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('status'); // 'pending', 'confirmed', 'used', 'cancelled', 'refunded'
            $table->string('payment_method'); // 'd17', 'konnect', 'paymee', 'sadad', 'stripe'
            $table->string('payment_status'); // 'pending', 'paid', 'failed', 'refunded'
            $table->string('payment_transaction_id')->nullable();
            $table->string('qr_code')->unique()->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('attendee_info')->nullable(); // Name, ID for ticket holder
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['match_id', 'status']);
            $table->index(['ticket_number']);
            $table->index(['qr_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_purchases');
        Schema::dropIfExists('tickets');
    }
};
