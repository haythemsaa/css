<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('gift_campaigns')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Gift details
            $table->string('gift_type'); // voucher, physical, digital, event_invite
            $table->string('gift_name');
            $table->text('gift_description')->nullable();
            $table->string('gift_image')->nullable();
            $table->decimal('gift_value', 10, 2)->nullable(); // TND

            // Voucher details (if applicable)
            $table->string('voucher_code')->unique()->nullable();
            $table->decimal('voucher_amount', 10, 2)->nullable();
            $table->timestamp('voucher_expires_at')->nullable();

            // Physical gift delivery (if applicable)
            $table->text('shipping_address')->nullable();
            $table->string('tracking_number')->nullable();
            $table->enum('delivery_status', [
                'pending',
                'processing',
                'shipped',
                'delivered',
                'cancelled'
            ])->nullable();

            // Status
            $table->boolean('is_claimed')->default(false);
            $table->timestamp('claimed_at')->nullable();
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();

            // Notification
            $table->boolean('notification_sent')->default(false);
            $table->timestamp('notification_sent_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('campaign_id');
            $table->index('gift_type');
            $table->index('is_claimed');
            $table->index('delivery_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_distributions');
    }
};
