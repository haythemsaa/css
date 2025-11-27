<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        $players = [
            ['first_name' => 'Ahmed', 'last_name' => 'Ben Ali', 'position' => 'goalkeeper', 'number' => 1],
            ['first_name' => 'Mohamed', 'last_name' => 'Trabelsi', 'position' => 'defender', 'number' => 2],
            ['first_name' => 'Youssef', 'last_name' => 'Msakni', 'position' => 'midfielder', 'number' => 10],
            ['first_name' => 'Fakhreddine', 'last_name' => 'Ben Youssef', 'position' => 'forward', 'number' => 9],
            ['first_name' => 'Aymen', 'last_name' => 'Dahmen', 'position' => 'goalkeeper', 'number' => 16],
            ['first_name' => 'Dylan', 'last_name' => 'Bronn', 'position' => 'defender', 'number' => 3],
            ['first_name' => 'Aïssa', 'last_name' => 'Laïdouni', 'position' => 'midfielder', 'number' => 8],
            ['first_name' => 'Seifeddine', 'last_name' => 'Jaziri', 'position' => 'forward', 'number' => 11],
        ];

        foreach ($players as $playerData) {
            Player::create([
                'first_name' => $playerData['first_name'],
                'last_name' => $playerData['last_name'],
                'slug' => \Str::slug($playerData['first_name'] . '-' . $playerData['last_name']),
                'jersey_number' => $playerData['number'],
                'position' => $playerData['position'],
                'nationality' => 'Tunisian',
                'date_of_birth' => now()->subYears(rand(22, 32)),
                'height' => rand(170, 190),
                'weight' => rand(70, 85),
                'goals' => rand(0, 20),
                'assists' => rand(0, 15),
                'matches_played' => rand(10, 50),
                'status' => 'active',
            ]);
        }
    }
}
