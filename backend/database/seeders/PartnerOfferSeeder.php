<?php

namespace Database\Seeders;

use App\Models\PartnerOffer;
use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerOfferSeeder extends Seeder
{
    public function run(): void
    {
        $partners = Partner::all();

        $offerTemplates = [
            'restauration' => [
                ['title' => 'Menu midi', 'type' => 'standard', 'reduction' => 15, 'days' => 60],
                ['title' => 'Offre week-end', 'type' => 'standard', 'reduction' => 20, 'days' => 90],
                ['title' => 'Offre Ramadan', 'type' => 'seasonal', 'reduction' => 25, 'days' => 30],
            ],
            'hotellerie' => [
                ['title' => 'Réduction séjour', 'type' => 'standard', 'reduction' => 20, 'days' => 120],
                ['title' => 'Package week-end', 'type' => 'flash', 'reduction' => 30, 'days' => 15],
            ],
            'sport' => [
                ['title' => 'Abonnement 3 mois', 'type' => 'standard', 'reduction' => 20, 'days' => 90],
                ['title' => 'Réduction équipements', 'type' => 'standard', 'reduction' => 15, 'days' => 60],
                ['title' => 'Offre rentrée', 'type' => 'seasonal', 'reduction' => 25, 'days' => 30],
            ],
            'shopping' => [
                ['title' => 'Réduction achats', 'type' => 'standard', 'reduction' => 10, 'days' => 90],
                ['title' => 'Soldes exclusifs', 'type' => 'flash', 'reduction' => 30, 'days' => 7],
            ],
            'services' => [
                ['title' => 'Première visite', 'type' => 'standard', 'reduction' => 20, 'days' => 60],
                ['title' => 'Forfait fidélité', 'type' => 'exclusive', 'reduction' => 25, 'days' => 120],
            ],
            'loisirs' => [
                ['title' => 'Réduction billets', 'type' => 'standard', 'reduction' => 20, 'days' => 60],
                ['title' => 'Offre groupe', 'type' => 'standard', 'reduction' => 25, 'days' => 90],
            ],
            'education' => [
                ['title' => 'Inscription cours', 'type' => 'standard', 'reduction' => 15, 'days' => 90],
                ['title' => 'Forfait annuel', 'type' => 'exclusive', 'reduction' => 30, 'days' => 180],
            ],
            'sante' => [
                ['title' => 'Réduction parapharmacie', 'type' => 'standard', 'reduction' => 10, 'days' => 90],
                ['title' => 'Check-up complet', 'type' => 'standard', 'reduction' => 20, 'days' => 60],
            ],
        ];

        foreach ($partners as $partner) {
            $categorySlug = $partner->category->slug;
            $templates = $offerTemplates[$categorySlug] ?? $offerTemplates['shopping'];

            foreach ($templates as $index => $template) {
                $slug = \Illuminate\Support\Str::slug($template['title'] . '-' . $partner->slug . '-' . $index);
                PartnerOffer::create([
                    'partner_id' => $partner->id,
                    'title' => $template['title'],
                    'slug' => $slug,
                    'description' => "Profitez de {$template['reduction']}% de réduction chez {$partner->name}. Offre valable pour les membres Premium et Socios.",
                    'offer_type' => $template['type'],
                    'reduction_type' => 'percentage',
                    'reduction_value' => $template['reduction'],
                    'valid_from' => now()->subDays(rand(0, 30)),
                    'valid_until' => now()->addDays($template['days']),
                    'stock_available' => rand(50, 200),
                    'stock_used' => rand(0, 30),
                    'status' => 'active',
                    'is_featured' => $index === 0 && rand(1, 3) === 1,
                    'display_order' => $index + 1,
                ]);
            }
        }
    }
}
