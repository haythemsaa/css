<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\PartnerCategory;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = PartnerCategory::where('slug', 'restaurants-alimentation')->first();
        $hotels = PartnerCategory::where('slug', 'hotels-tourisme')->first();
        $sports = PartnerCategory::where('slug', 'sports-bien-etre')->first();

        $partners = [
            [
                'name' => 'Restaurant Le Phénicien',
                'category_id' => $restaurants->id,
                'city' => 'Sfax',
                'address' => 'Avenue Habib Bourguiba, Sfax',
                'reduction_value_premium' => 15,
                'reduction_value_socios' => 25,
            ],
            [
                'name' => 'Hôtel Les Oliviers Palace',
                'category_id' => $hotels->id,
                'city' => 'Sfax',
                'address' => 'Route de l\'Aéroport, Sfax',
                'reduction_value_premium' => 10,
                'reduction_value_socios' => 20,
            ],
            [
                'name' => 'Fitness Plus Gym',
                'category_id' => $sports->id,
                'city' => 'Sfax',
                'address' => 'Avenue Majida Boulila, Sfax',
                'reduction_value_premium' => 15,
                'reduction_value_socios' => 30,
            ],
        ];

        foreach ($partners as $partnerData) {
            Partner::create([
                'name' => $partnerData['name'],
                'slug' => \Str::slug($partnerData['name']),
                'category_id' => $partnerData['category_id'],
                'city' => $partnerData['city'],
                'address' => $partnerData['address'],
                'latitude' => 34.7406 + (rand(-100, 100) / 1000),
                'longitude' => 10.7603 + (rand(-100, 100) / 1000),
                'reduction_value_premium' => $partnerData['reduction_value_premium'],
                'reduction_value_socios' => $partnerData['reduction_value_socios'],
                'reduction_type' => 'percentage',
                'commission_percentage' => 5.00,
                'is_active' => true,
                'is_featured' => rand(0, 1),
            ]);
        }
    }
}
