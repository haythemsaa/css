<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+216' . fake()->numerify('########'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'user_type' => fake()->randomElement(['free', 'free', 'free', 'premium']),
            'city' => fake()->randomElement(['Sfax', 'Tunis', 'Sousse', 'Monastir', 'Mahdia']),
            'country' => 'Tunisia',
            'loyalty_points' => fake()->numberBetween(0, 1000),
            'loyalty_level' => 'bronze',
            'referral_code' => Str::upper(Str::random(8)),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'premium',
            'subscription_active' => true,
            'subscription_starts_at' => now(),
            'subscription_expires_at' => now()->addYear(),
        ]);
    }

    public function socios(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'socios',
            'socios_number' => 'SOCIOS' . fake()->unique()->numerify('####'),
            'socios_verified' => true,
            'socios_membership_date' => now()->subYears(fake()->numberBetween(1, 10)),
            'loyalty_points' => fake()->numberBetween(1000, 10000),
            'loyalty_level' => fake()->randomElement(['silver', 'gold', 'platinum']),
        ]);
    }
}
