<?php

namespace Database\Factories;

use App\Models\ReductionCode;
use App\Models\User;
use App\Models\PartnerOffer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReductionCode>
 */
class ReductionCodeFactory extends Factory
{
    protected $model = ReductionCode::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['qr', 'promo', 'nfc'];
        $statuses = ['active', 'used', 'expired'];

        return [
            'user_id' => User::factory(),
            'offer_id' => PartnerOffer::factory(),
            'code' => strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(6)),
            'type' => fake()->randomElement($types),
            'status' => fake()->randomElement($statuses),
            'expires_at' => now()->addDays(30),
            'used_at' => null,
        ];
    }
}
