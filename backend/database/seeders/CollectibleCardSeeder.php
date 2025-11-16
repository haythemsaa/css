<?php

namespace Database\Seeders;

use App\Models\CollectibleCard;
use Illuminate\Database\Seeder;

class CollectibleCardSeeder extends Seeder
{
    public function run(): void
    {
        $playerCards = [
            // Légendaires (3)
            ['name' => 'Aymen Dahmen - Gardien International', 'rarity' => 'legendary', 'supply' => 100],
            ['name' => 'Firas Chaouat - Maestro', 'rarity' => 'legendary', 'supply' => 100],
            ['name' => 'Pedro Sá - Buteur de Légende', 'rarity' => 'legendary', 'supply' => 100],

            // Épiques (7)
            ['name' => 'Mohamed Ali Ben Romdhane - Talent', 'rarity' => 'epic', 'supply' => 250],
            ['name' => 'Kingsley Eduwo - Le Roc', 'rarity' => 'epic', 'supply' => 250],
            ['name' => 'Houssem Dagdoug - Latéral Offensif', 'rarity' => 'epic', 'supply' => 250],
            ['name' => 'Aymen Harzi - Ailier Rapide', 'rarity' => 'epic', 'supply' => 250],
            ['name' => 'Kingsley Sokari - Dribbleur', 'rarity' => 'epic', 'supply' => 250],
            ['name' => 'Hazem Haj Hassen - Milieu Créatif', 'rarity' => 'epic', 'supply' => 250],
            ['name' => 'Mohamed Dhaoui - Capitaine', 'rarity' => 'epic', 'supply' => 250],

            // Rares (10)
            ['name' => 'Ali Jemal - Gardien Remplaçant', 'rarity' => 'rare', 'supply' => 500],
            ['name' => 'Hamza Jelassi - Latéral Droit', 'rarity' => 'rare', 'supply' => 500],
            ['name' => 'Rubin Hebaj - Défenseur Polyvalent', 'rarity' => 'rare', 'supply' => 500],
            ['name' => 'Aymen Abdennour - Milieu Défensif', 'rarity' => 'rare', 'supply' => 500],
            ['name' => 'Ghazi Ayadi - Milieu Polyvalent', 'rarity' => 'rare', 'supply' => 500],
            ['name' => 'Youssef Becha - Créateur', 'rarity' => 'rare', 'supply' => 500],
            ['name' => 'Iheb Mbarki - Attaquant', 'rarity' => 'rare', 'supply' => 500],
            ['name' => 'Firas Sekouhi - Jeune Talent', 'rarity' => 'rare', 'supply' => 500],
            ['name' => 'Zied Aloui - Ailier', 'rarity' => 'rare', 'supply' => 500],
            ['name' => 'Seif Teka - Espoir', 'rarity' => 'rare', 'supply' => 500],

            // Communes (10)
            ['name' => 'Mohamed Sedki - Gardien Espoir', 'rarity' => 'common', 'supply' => 1000],
            ['name' => 'Adem Khalfa - Défenseur Espoir', 'rarity' => 'common', 'supply' => 1000],
            ['name' => 'Hamza Agrebi - Attaquant Espoir', 'rarity' => 'common', 'supply' => 1000],
            ['name' => 'CSS - Équipe Complète', 'rarity' => 'common', 'supply' => 1000],
            ['name' => 'CSS - Formation Tactique', 'rarity' => 'common', 'supply' => 1000],
            ['name' => 'CSS - Échauffement', 'rarity' => 'common', 'supply' => 1000],
            ['name' => 'CSS - Célébration de But', 'rarity' => 'common', 'supply' => 1000],
            ['name' => 'CSS - Supporters', 'rarity' => 'common', 'supply' => 1000],
            ['name' => 'CSS - Maillot Domicile', 'rarity' => 'common', 'supply' => 1000],
            ['name' => 'CSS - Maillot Extérieur', 'rarity' => 'common', 'supply' => 1000],
        ];

        $historicCards = [
            // Légendaires (2)
            ['name' => 'Victoire Ligue des Champions CAF 2007', 'rarity' => 'legendary', 'supply' => 75],
            ['name' => 'Champion de Tunisie - Saison Invincible', 'rarity' => 'legendary', 'supply' => 75],

            // Épiques (5)
            ['name' => 'Ali Kaabi - Légende du CSS', 'rarity' => 'epic', 'supply' => 200],
            ['name' => 'Victoire Coupe de Tunisie 2009', 'rarity' => 'epic', 'supply' => 200],
            ['name' => 'Derby Historique CSS vs EST', 'rarity' => 'epic', 'supply' => 200],
            ['name' => 'Remontada Mémorable', 'rarity' => 'epic', 'supply' => 200],
            ['name' => '100 ans du CSS - Édition Spéciale', 'rarity' => 'epic', 'supply' => 200],

            // Rares (5)
            ['name' => 'Premier Match au Stade Taieb Mhiri', 'rarity' => 'rare', 'supply' => 400],
            ['name' => 'Qualification Ligue des Champions', 'rarity' => 'rare', 'supply' => 400],
            ['name' => 'Record de Buts en Championnat', 'rarity' => 'rare', 'supply' => 400],
            ['name' => 'Plus Grande Victoire à Domicile', 'rarity' => 'rare', 'supply' => 400],
            ['name' => 'Saison 50 Buts', 'rarity' => 'rare', 'supply' => 400],

            // Communes (3)
            ['name' => 'Fondation du CSS - 1928', 'rarity' => 'common', 'supply' => 800],
            ['name' => 'Palmarès du CSS', 'rarity' => 'common', 'supply' => 800],
            ['name' => 'Logo Historique CSS', 'rarity' => 'common', 'supply' => 800],
        ];

        $stadiumCards = [
            // Légendaire (1)
            ['name' => 'Stade Taieb Mhiri - Vue Panoramique Nuit', 'rarity' => 'legendary', 'supply' => 50],

            // Épique (2)
            ['name' => 'Stade Taieb Mhiri - Tribune Pleine', 'rarity' => 'epic', 'supply' => 150],
            ['name' => 'Pelouse du Stade Taieb Mhiri', 'rarity' => 'epic', 'supply' => 150],

            // Rares (2)
            ['name' => 'Vestiaires CSS', 'rarity' => 'rare', 'supply' => 300],
            ['name' => 'Centre d\'Entraînement', 'rarity' => 'rare', 'supply' => 300],
        ];

        $season = '2024-2025';

        // Créer les cartes de joueurs
        foreach ($playerCards as $index => $card) {
            CollectibleCard::create([
                'name' => $card['name'],
                'description' => "Carte de collection officielle du Club Sportif Sfaxien - Saison {$season}",
                'category' => 'player',
                'rarity' => $card['rarity'],
                'image' => "https://css.tn/cards/player-{$index}.jpg",
                'total_supply' => $card['supply'],
                'release_date' => now()->subDays(rand(0, 180)),
            ]);
        }

        // Créer les cartes historiques
        foreach ($historicCards as $index => $card) {
            CollectibleCard::create([
                'name' => $card['name'],
                'description' => "Carte commémorative d'un moment historique du Club Sportif Sfaxien",
                'category' => 'historic',
                'rarity' => $card['rarity'],
                'image' => "https://css.tn/cards/historic-{$index}.jpg",
                'total_supply' => $card['supply'],
                'release_date' => now()->subDays(rand(0, 365)),
            ]);
        }

        // Créer les cartes de stade
        foreach ($stadiumCards as $index => $card) {
            CollectibleCard::create([
                'name' => $card['name'],
                'description' => "Carte de collection représentant le temple du CSS",
                'category' => 'stadium',
                'rarity' => $card['rarity'],
                'image' => "https://css.tn/cards/stadium-{$index}.jpg",
                'total_supply' => $card['supply'],
                'release_date' => now()->subDays(rand(0, 90)),
            ]);
        }
    }
}
