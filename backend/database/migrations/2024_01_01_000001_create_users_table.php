<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->enum('user_type', ['free', 'premium', 'socios'])->default('free');
            $table->string('profile_photo')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Tunisia');

            // Socios specific fields
            $table->string('socios_number')->unique()->nullable();
            $table->boolean('socios_verified')->default(false);
            $table->date('socios_membership_date')->nullable();
            $table->string('socios_card_image')->nullable();

            // Premium subscription
            $table->timestamp('subscription_starts_at')->nullable();
            $table->timestamp('subscription_expires_at')->nullable();
            $table->enum('subscription_type', ['monthly', 'yearly'])->nullable();
            $table->boolean('subscription_active')->default(false);

            // Loyalty program
            $table->integer('loyalty_points')->default(0);
            $table->enum('loyalty_level', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze');

            // Social login
            $table->string('facebook_id')->unique()->nullable();
            $table->string('google_id')->unique()->nullable();

            // Preferences
            $table->json('notification_preferences')->nullable();
            $table->string('preferred_language')->default('fr');

            // Referral
            $table->string('referral_code')->unique();
            $table->foreignId('referred_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('referral_count')->default(0);

            // Security
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->boolean('is_banned')->default(false);
            $table->text('ban_reason')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_type');
            $table->index('socios_verified');
            $table->index('subscription_active');
            $table->index('loyalty_level');
            $table->index('referral_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
