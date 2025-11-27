<?php

namespace Database\Factories;

use App\Models\AuctionProduct;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AuctionProductFactory extends Factory
{
    protected $model = AuctionProduct::class;

    public function definition(): array
    {
        $startingPrice = $this->faker->randomFloat(2, 100, 1000);
        $currentBid = $this->faker->boolean(70) ? $this->faker->randomFloat(2, $startingPrice, $startingPrice * 2) : 0;

        return [
            'title' => $this->faker->sentence(4),
            'slug' => Str::slug($this->faker->sentence(3)),
            'description' => $this->faker->paragraph(),
            'images' => ['/images/auction-' . $this->faker->numberBetween(1, 10) . '.jpg'],
            'category' => $this->faker->randomElement(['collectibles', 'memorabilia', 'experiences', 'signed_items']),
            'starting_price' => $startingPrice,
            'reserve_price' => $this->faker->boolean(60) ? $startingPrice * 1.5 : null,
            'current_bid' => $currentBid,
            'buy_now_price' => $this->faker->boolean(50) ? $startingPrice * 3 : null,
            'bid_increment' => $this->faker->randomElement([5, 10, 20, 50]),
            'total_bids' => $this->faker->numberBetween(0, 50),
            'start_time' => now()->subDays($this->faker->numberBetween(0, 5)),
            'end_time' => now()->addDays($this->faker->numberBetween(1, 10)),
            'is_featured' => $this->faker->boolean(20),
            'auto_extend' => true,
            'auto_extend_minutes' => 5,
            'status' => $this->faker->randomElement(['scheduled', 'active', 'ended']),
            'terms_conditions' => 'Standard auction terms apply.',
            'metadata' => [
                'condition' => $this->faker->randomElement(['Neuf', 'Excellent', 'Bon', 'Utilisé']),
                'authenticity' => 'Certificat CSS officiel',
            ],
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_time' => now()->subDay(),
            'end_time' => now()->addDays(5),
        ]);
    }

    public function ended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ended',
            'start_time' => now()->subDays(10),
            'end_time' => now()->subDay(),
        ]);
    }
}
