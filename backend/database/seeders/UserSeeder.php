<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'CSS',
            'email' => 'admin@css-sfax.tn',
            'phone' => '+21612345678',
            'password' => Hash::make('password'),
            'user_type' => 'free',
            'email_verified_at' => now(),
            'referral_code' => Str::upper(Str::random(8)),
        ]);

        // Premium user
        User::create([
            'first_name' => 'Premium',
            'last_name' => 'User',
            'email' => 'premium@css-sfax.tn',
            'phone' => '+21612345679',
            'password' => Hash::make('password'),
            'user_type' => 'premium',
            'subscription_active' => true,
            'subscription_starts_at' => now(),
            'subscription_expires_at' => now()->addYear(),
            'email_verified_at' => now(),
            'referral_code' => Str::upper(Str::random(8)),
        ]);

        // Socios user
        User::create([
            'first_name' => 'Socios',
            'last_name' => 'Member',
            'email' => 'socios@css-sfax.tn',
            'phone' => '+21612345680',
            'password' => Hash::make('password'),
            'user_type' => 'socios',
            'socios_number' => 'SOCIOS001',
            'socios_verified' => true,
            'socios_membership_date' => now()->subYears(5),
            'loyalty_points' => 5000,
            'loyalty_level' => 'platinum',
            'email_verified_at' => now(),
            'referral_code' => Str::upper(Str::random(8)),
        ]);

        // Create 20 random free users
        User::factory(20)->create();
    }
}
