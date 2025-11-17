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
        $types = ['standard', 'flash', 'vip'];

        return [
            'partner_id' => Partner::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 1000),
            'description' => fake()->paragraph(),
            'offer_type' => fake()->randomElement($types),
            'reduction_percentage' => fake()->randomElement([10, 15, 20, 25]),
            'conditions' => fake()->sentence(),
            'stock' => fake()->numberBetween(5, 100),
            'max_uses_per_user' => fake()->numberBetween(1, 5),
            'valid_from' => now(),
            'valid_until' => now()->addDays(fake()->numberBetween(7, 90)),
            'is_active' => true,
        ];
    }
}
