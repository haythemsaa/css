<?php

namespace Database\Factories;

use App\Models\PartnerOffer;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PartnerOffer>
 */
class PartnerOfferFactory extends Factory
{
    protected $model = PartnerOffer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);
        $types = ['standard', 'flash', 'seasonal', 'exclusive'];

        return [
            'partner_id' => Partner::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 1000),
            'description' => fake()->paragraph(),
            'offer_type' => fake()->randomElement($types),
            'reduction_value' => fake()->randomFloat(2, 10, 25),
            'reduction_type' => 'percentage',
            'valid_from' => now(),
            'valid_until' => now()->addDays(fake()->numberBetween(7, 90)),
            'stock_available' => fake()->numberBetween(5, 100),
            'stock_used' => 0,
            'user_limit_per_day' => fake()->numberBetween(1, 3),
            'user_limit_per_month' => fake()->numberBetween(5, 10),
            'membership_required' => fake()->randomElement(['premium', 'socios', 'both']),
            'status' => 'active',
            'is_featured' => fake()->boolean(20),
            'display_order' => fake()->numberBetween(0, 10),
        ];
    }
}
