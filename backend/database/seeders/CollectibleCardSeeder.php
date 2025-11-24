<?php

namespace Database\Seeders;

use App\Models\CollectibleCard;
use App\Models\Player;
use Illuminate\Database\Seeder;

class CollectibleCardSeeder extends Seeder
{
    public function run(): void
    {
        $players = Player::limit(10)->get();

        if ($players->isEmpty()) {
            $this->command->warn('No players found. Run PlayerSeeder first.');
            return;
        }

        $rarities = ['common', 'uncommon', 'rare', 'epic', 'legendary'];
        $seasons = ['2023-2024', '2024-2025'];

        foreach ($players as $index => $player) {
            CollectibleCard::create([
                'name' => $player->name . ' - ' . $seasons[array_rand($seasons)],
                'description' => "Carte de collection de {$player->name}, joueur emblématique du CSS",
                'card_type' => 'player',
                'rarity' => $rarities[min($index, count($rarities) - 1)],
                'season' => $seasons[array_rand($seasons)],
                'player_id' => $player->id,
                'image_url' => $player->photo ?? '/images/cards/default.png',
                'stats' => [
                    'goals' => $player->goals ?? rand(0, 20),
                    'assists' => $player->assists ?? rand(0, 15),
                    'appearances' => $player->appearances ?? rand(10, 40),
                    'rating' => rand(70, 95),
                ],
                'release_date' => now()->subMonths(rand(1, 12)),
                'total_supply' => match($rarities[min($index, count($rarities) - 1)]) {
                    'common' => 1000,
                    'uncommon' => 500,
                    'rare' => 200,
                    'epic' => 50,
                    'legendary' => 10,
                    default => 1000,
                },
                'is_tradeable' => true,
                'is_active' => true,
            ]);
        }

        // Add some special event cards
        $eventCards = [
            [
                'name' => 'Centenaire CSS 1928-2028',
                'description' => 'Carte commémorative du centenaire du club',
                'card_type' => 'special',
                'rarity' => 'legendary',
                'season' => '2024-2025',
                'image_url' => '/images/cards/centenary.png',
                'stats' => ['years' => 100, 'trophies' => 28],
                'release_date' => now(),
                'total_supply' => 100,
                'is_tradeable' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Champion 2024',
                'description' => 'Carte célébrant le titre de champion',
                'card_type' => 'achievement',
                'rarity' => 'epic',
                'season' => '2023-2024',
                'image_url' => '/images/cards/champion.png',
                'stats' => ['position' => 1, 'points' => 75],
                'release_date' => now()->subMonths(6),
                'total_supply' => 250,
                'is_tradeable' => true,
                'is_active' => true,
            ],
        ];

        foreach ($eventCards as $card) {
            CollectibleCard::create($card);
        }

        $this->command->info('Cartes à collectionner créées avec succès');
    }
}
