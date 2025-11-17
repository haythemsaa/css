<?php

namespace Database\Factories;

use App\Models\PartnerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PartnerCategory>
 */
class PartnerCategoryFactory extends Factory
{
    protected $model = PartnerCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nameFr = fake()->words(2, true);

        return [
            'name_fr' => $nameFr,
            'name_ar' => 'تصنيف',
            'slug' => Str::slug($nameFr) . '-' . fake()->unique()->numberBetween(1, 100),
            'icon' => fake()->randomElement(['🍽️', '🏃', '🛍️', '🏥', '🎨', '🏨', '🚗', '💼']),
            'color' => fake()->hexColor(),
            'description' => fake()->sentence(),
            'display_order' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}
