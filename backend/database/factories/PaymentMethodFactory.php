<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement(['D17', 'Konnect', 'Flouci', 'Visa', 'Mastercard']);

        return [
            'name' => $name,
            'code' => strtolower($name),
            'type' => $this->faker->randomElement(['mobile_wallet', 'bank_card', 'bank_transfer']),
            'description' => $this->faker->sentence(),
            'logo_url' => '/images/payment-' . strtolower($name) . '.png',
            'provider' => $this->faker->company(),
            'is_active' => true,
            'is_default' => false,
            'supported_currencies' => ['TND'],
            'min_amount' => $this->faker->randomFloat(2, 1, 10),
            'max_amount' => $this->faker->randomFloat(2, 5000, 50000),
            'transaction_fee' => $this->faker->randomFloat(2, 0, 5),
            'transaction_fee_percentage' => $this->faker->randomFloat(2, 0, 3),
            'config' => null,
            'processing_time' => $this->faker->randomElement(['Instantané', '1-2 heures', '1-3 jours']),
            'supports_refund' => $this->faker->boolean(80),
            'instructions' => $this->faker->sentence(),
            'display_order' => $this->faker->numberBetween(1, 10),
            'available_for' => ['donations', 'products', 'tickets', 'auctions'],
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
            'is_active' => true,
        ]);
    }
}
