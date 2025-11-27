<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'Journée Portes Ouvertes - Stade Taïeb Mhiri',
                'slug' => 'journee-portes-ouvertes-stade',
                'description' => 'Venez découvrir les coulisses du stade et rencontrer les joueurs !',
                'event_type' => 'training',
                'venue' => 'Stade Taïeb Mhiri',
                'address' => 'Avenue Habib Bourguiba, Sfax',
                'latitude' => 34.750,
                'longitude' => 10.720,
                'start_datetime' => now()->addWeek(),
                'end_datetime' => now()->addWeek()->addHours(4),
                'max_attendees' => 500,
                'requires_registration' => true,
                'is_free' => true,
                'status' => 'upcoming',
                'is_featured' => true,
            ],
            [
                'title' => 'Meet & Greet avec les Joueurs',
                'slug' => 'meet-greet-joueurs',
                'description' => 'Une opportunité unique de rencontrer vos joueurs préférés et obtenir des autographes !',
                'event_type' => 'meet_greet',
                'venue' => 'Centre d\'Entraînement CSS',
                'address' => 'Route de Tunis, Sfax',
                'latitude' => 34.755,
                'longitude' => 10.725,
                'start_datetime' => now()->addDays(10),
                'end_datetime' => now()->addDays(10)->addHours(2),
                'max_attendees' => 100,
                'requires_registration' => true,
                'is_free' => false,
                'price' => 20.00,
                'status' => 'upcoming',
                'is_featured' => true,
            ],
            [
                'title' => 'Conférence de Presse Pre-Match',
                'slug' => 'conference-presse-pre-match',
                'description' => 'Conférence de presse avant le match crucial contre l\'EST',
                'event_type' => 'conference',
                'venue' => 'Stade Taïeb Mhiri - Salle de Presse',
                'address' => 'Avenue Habib Bourguiba, Sfax',
                'start_datetime' => now()->addDays(5),
                'end_datetime' => now()->addDays(5)->addHour(),
                'requires_registration' => false,
                'is_free' => true,
                'status' => 'upcoming',
                'is_featured' => false,
            ],
            [
                'title' => 'Fête du Centenaire CSS',
                'slug' => 'fete-centenaire-css',
                'description' => 'Grande fête pour célébrer le centenaire du club avec concerts et animations',
                'event_type' => 'party',
                'venue' => 'Stade Taïeb Mhiri',
                'address' => 'Avenue Habib Bourguiba, Sfax',
                'start_datetime' => now()->addMonth(),
                'end_datetime' => now()->addMonth()->addHours(6),
                'max_attendees' => 5000,
                'requires_registration' => true,
                'is_free' => false,
                'price' => 15.00,
                'status' => 'upcoming',
                'is_featured' => true,
            ],
            [
                'title' => 'Entraînement Ouvert au Public',
                'slug' => 'entrainement-ouvert-public',
                'description' => 'Assistez à une séance d\'entraînement de l\'équipe première',
                'event_type' => 'training',
                'venue' => 'Centre d\'Entraînement CSS',
                'address' => 'Route de Tunis, Sfax',
                'start_datetime' => now()->addDays(3),
                'end_datetime' => now()->addDays(3)->addHours(2),
                'max_attendees' => 300,
                'requires_registration' => true,
                'is_free' => true,
                'status' => 'upcoming',
                'is_featured' => false,
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }

        $this->command->info('Events créés avec succès');
    }
}
