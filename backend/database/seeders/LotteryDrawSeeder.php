<?php

namespace Database\Seeders;

use App\Models\LotteryDraw;
use Illuminate\Database\Seeder;

class LotteryDrawSeeder extends Seeder
{
    public function run(): void
    {
        $draws = [
            [
                'name' => 'Tombola Ramadan 2025',
                'description' => 'Gagnez un maillot dédicacé par toute l\'équipe CSS',
                'ticket_price' => 5.000,
                'max_tickets' => 500,
                'tickets_sold' => 0,
                'prize_description' => 'Maillot CSS 2024-2025 dédicacé + Photo avec l\'équipe',
                'prize_value' => 500.000,
                'draw_date' => now()->addDays(30),
                'status' => 'active',
                'terms' => 'Un gagnant sera tiré au sort le jour du match CSS vs EST',
            ],
            [
                'name' => 'Grande Loterie Anniversaire CSS',
                'description' => 'Célébrez les 100 ans du club avec nous !',
                'ticket_price' => 10.000,
                'max_tickets' => 1000,
                'tickets_sold' => 0,
                'prize_description' => '2 places VIP pour tous les matchs à domicile saison prochaine',
                'prize_value' => 2000.000,
                'draw_date' => now()->addMonths(2),
                'status' => 'active',
                'terms' => 'Les places VIP incluent accès lounge et parking gratuit',
            ],
            [
                'name' => 'Tombola Express Weekend',
                'description' => 'Tirage rapide ce weekend !',
                'ticket_price' => 3.000,
                'max_tickets' => 200,
                'tickets_sold' => 0,
                'prize_description' => 'Écharpe CSS collector + Casquette CSS',
                'prize_value' => 100.000,
                'draw_date' => now()->addDays(3),
                'status' => 'active',
                'terms' => 'Tirage samedi à 18h',
            ],
        ];

        foreach ($draws as $draw) {
            LotteryDraw::create($draw);
        }

        $this->command->info('Tirages de loterie créés avec succès');
    }
}
