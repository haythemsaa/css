<?php

namespace Database\Seeders;

use App\Models\FootballMatch;
use Illuminate\Database\Seeder;

class FootballMatchSeeder extends Seeder
{
    public function run(): void
    {
        $opponents = [
            ['name' => 'Espérance Sportive de Tunis', 'short' => 'EST'],
            ['name' => 'Club Africain', 'short' => 'CA'],
            ['name' => 'Étoile Sportive du Sahel', 'short' => 'ESS'],
            ['name' => 'Club Sportif Bizertin', 'short' => 'CSB'],
            ['name' => 'US Monastir', 'short' => 'USM'],
            ['name' => 'CS Hammam-Lif', 'short' => 'CSHL'],
            ['name' => 'Stade Tunisien', 'short' => 'ST'],
            ['name' => 'US Ben Guerdane', 'short' => 'USBG'],
            ['name' => 'AS Soliman', 'short' => 'ASS'],
            ['name' => 'CA Bizertin', 'short' => 'CAB'],
        ];

        $competitions = [
            'Ligue 1 Tunisie',
            'Coupe de Tunisie',
            'Ligue des Champions CAF',
        ];

        $stadiums = [
            'Stade Taieb Mhiri, Sfax',
            'Stade Olympique de Radès, Tunis',
            'Stade Taïeb Mhiri, Sfax',
            'Stade Mustapha Ben Jannet, Monastir',
        ];

        // Matchs terminés (12)
        for ($i = 0; $i < 12; $i++) {
            $opponent = $opponents[array_rand($opponents)];
            $isHome = rand(0, 1) === 1;
            $cssScore = rand(0, 4);
            $opponentScore = rand(0, 3);

            FootballMatch::create([
                'match_date' => now()->subDays(rand(5, 90)),
                'kick_off_time' => now()->subDays(rand(5, 90))->setTime(rand(15, 20), rand(0, 59)),
                'competition' => $competitions[array_rand($competitions)],
                'opponent' => $opponent['name'],
                'stadium' => $isHome ? 'Stade Taieb Mhiri, Sfax' : $stadiums[array_rand($stadiums)],
                'home_away' => $isHome ? 'home' : 'away',
                'css_score' => $cssScore,
                'opponent_score' => $opponentScore,
                'status' => 'finished',
                'attendance' => $isHome ? rand(8000, 15000) : rand(5000, 12000),
            ]);
        }

        // Matchs en cours (2)
        for ($i = 0; $i < 2; $i++) {
            $opponent = $opponents[array_rand($opponents)];
            $isHome = rand(0, 1) === 1;

            FootballMatch::create([
                'match_date' => now(),
                'kick_off_time' => now()->subMinutes(rand(10, 60)),
                'competition' => $competitions[array_rand($competitions)],
                'opponent' => $opponent['name'],
                'stadium' => $isHome ? 'Stade Taieb Mhiri, Sfax' : $stadiums[array_rand($stadiums)],
                'home_away' => $isHome ? 'home' : 'away',
                'css_score' => rand(0, 2),
                'opponent_score' => rand(0, 2),
                'status' => 'live',
                'attendance' => $isHome ? rand(10000, 15000) : null,
            ]);
        }

        // Matchs à venir (6)
        for ($i = 0; $i < 6; $i++) {
            $opponent = $opponents[array_rand($opponents)];
            $isHome = rand(0, 1) === 1;
            $daysAhead = rand(2, 30);

            FootballMatch::create([
                'match_date' => now()->addDays($daysAhead),
                'kick_off_time' => now()->addDays($daysAhead)->setTime(rand(15, 20), rand(0, 59)),
                'competition' => $competitions[array_rand($competitions)],
                'opponent' => $opponent['name'],
                'stadium' => $isHome ? 'Stade Taieb Mhiri, Sfax' : $stadiums[array_rand($stadiums)],
                'home_away' => $isHome ? 'home' : 'away',
                'status' => 'scheduled',
            ]);
        }
    }
}
