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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('photo')->nullable()->after('phone');
            $table->date('birth_date')->nullable()->after('photo');
            $table->string('city')->nullable()->after('birth_date');
            $table->string('governorate')->nullable()->after('city');

            // User type: free, premium, socios
            $table->enum('user_type', ['free', 'premium', 'socios'])->default('free')->after('governorate');

            // Socios fields
            $table->string('socios_number')->nullable()->unique()->after('user_type');
            $table->boolean('socios_verified')->default(false)->after('socios_number');
            $table->timestamp('socios_verified_at')->nullable()->after('socios_verified');

            // Subscription fields
            $table->enum('subscription_status', ['active', 'inactive', 'cancelled', 'expired'])->nullable()->after('socios_verified_at');
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_status');

            // Loyalty fields
            $table->integer('loyalty_points')->default(0)->after('subscription_expires_at');
            $table->enum('loyalty_level', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze')->after('loyalty_points');

            // Additional fields
            $table->json('preferences')->nullable()->after('loyalty_level');
            $table->boolean('is_active')->default(true)->after('preferences');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'photo', 'birth_date', 'city', 'governorate',
                'user_type', 'socios_number', 'socios_verified', 'socios_verified_at',
                'subscription_status', 'subscription_expires_at',
                'loyalty_points', 'loyalty_level',
                'preferences', 'is_active', 'last_login_at'
            ]);
        });
    }
};
