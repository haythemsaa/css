<?php

namespace Database\Factories;

use App\Models\DonationGoal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DonationGoalFactory extends Factory
{
    protected $model = DonationGoal::class;

    public function definition(): array
    {
        $targetAmount = $this->faker->randomFloat(2, 10000, 500000);
        $currentAmount = $this->faker->randomFloat(2, 0, $targetAmount * 0.8);

        return [
            'title' => $this->faker->sentence(6),
            'slug' => Str::slug($this->faker->sentence(3)),
            'description' => $this->faker->paragraph(2),
            'full_details' => $this->faker->paragraphs(3, true),
            'category' => $this->faker->randomElement(['litigation', 'player_transfer', 'stadium_renovation', 'youth_academy', 'equipment', 'debt_payment']),
            'target_amount' => $targetAmount,
            'current_amount' => $currentAmount,
            'donors_count' => $this->faker->numberBetween(0, 1000),
            'min_donation' => $this->faker->randomElement([5, 10, 20, 50]),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'start_date' => now()->subDays($this->faker->numberBetween(0, 30)),
            'end_date' => $this->faker->boolean(70) ? now()->addDays($this->faker->numberBetween(30, 180)) : null,
            'status' => 'active',
            'is_featured' => $this->faker->boolean(30),
            'featured_image' => '/images/goal-' . $this->faker->numberBetween(1, 10) . '.jpg',
            'gallery_images' => null,
            'milestone_updates' => null,
            'impact_metrics' => $this->faker->sentence(),
            'thank_you_message' => $this->faker->sentence(),
            'show_donors' => $this->faker->boolean(80),
            'allow_anonymous' => $this->faker->boolean(90),
            'rewards' => null,
        ];
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
            'is_featured' => true,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_amount' => $attributes['target_amount'],
            'status' => 'completed',
        ]);
    }
}
