<?php

namespace Database\Seeders;

use App\Models\Match;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $matches = Match::where('status', 'scheduled')
            ->where('match_date', '>', now())
            ->orderBy('match_date')
            ->limit(5)
            ->get();

        if ($matches->isEmpty()) {
            $this->command->warn('No upcoming matches found. Run MatchSeeder first.');
            return;
        }

        foreach ($matches as $match) {
            // VIP Tickets
            Ticket::create([
                'match_id' => $match->id,
                'category' => 'vip',
                'section' => 'Loges VIP',
                'price' => 150.00,
                'total_quantity' => 50,
                'available_quantity' => 50,
                'is_available' => true,
                'sale_starts_at' => now(),
                'sale_ends_at' => $match->match_date->subHours(2),
                'benefits' => [
                    'parking_inclus' => true,
                    'acces_salon_vip' => true,
                    'buffet' => true,
                    'programme_match' => true,
                ],
            ]);

            // Tribune Tickets
            Ticket::create([
                'match_id' => $match->id,
                'category' => 'tribune',
                'section' => 'Tribune Centrale',
                'price' => 50.00,
                'total_quantity' => 500,
                'available_quantity' => 500,
                'is_available' => true,
                'sale_starts_at' => now(),
                'sale_ends_at' => $match->match_date->subHours(2),
                'benefits' => [
                    'vue_degagee' => true,
                    'siege_numerote' => true,
                ],
            ]);

            // Pelouse Tickets
            Ticket::create([
                'match_id' => $match->id,
                'category' => 'pelouse',
                'section' => 'Pelouse Nord',
                'price' => 20.00,
                'total_quantity' => 1000,
                'available_quantity' => 1000,
                'is_available' => true,
                'sale_starts_at' => now(),
                'sale_ends_at' => $match->match_date->subHours(2),
                'benefits' => [
                    'ambiance_fervente' => true,
                ],
            ]);

            // Family Tickets
            Ticket::create([
                'match_id' => $match->id,
                'category' => 'family',
                'section' => 'Tribune Famille',
                'price' => 35.00,
                'total_quantity' => 200,
                'available_quantity' => 200,
                'is_available' => true,
                'sale_starts_at' => now(),
                'sale_ends_at' => $match->match_date->subHours(2),
                'benefits' => [
                    'zone_securisee' => true,
                    'animation_enfants' => true,
                    'acces_mascotte' => true,
                ],
            ]);
        }

        $this->command->info('Billets créés avec succès pour ' . $matches->count() . ' matchs');
    }
}
