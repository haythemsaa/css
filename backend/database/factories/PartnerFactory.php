<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\PartnerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class PartnerFactory extends Factory
{
    protected $model = Partner::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();
        $cities = ['Sfax', 'Tunis', 'Sousse', 'Monastir'];

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 1000),
            'category_id' => PartnerCategory::factory(),
            'description' => fake()->paragraphs(2, true),
            'short_description' => fake()->sentence(),
            'logo' => fake()->imageUrl(200, 200, 'business'),
            'reduction_value_premium' => fake()->randomFloat(2, 10, 15),
            'reduction_value_socios' => fake()->randomFloat(2, 15, 25),
            'address' => fake()->address(),
            'city' => fake()->randomElement($cities),
            'postal_code' => fake()->postcode(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'website' => fake()->url(),
            'latitude' => fake()->latitude(10.0, 11.0),
            'longitude' => fake()->longitude(34.5, 35.5),
            'status' => 'active',
            'featured_order' => fake()->numberBetween(0, 10),
        ];
    }
}
