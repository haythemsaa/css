<?php

namespace Database\Seeders;

use App\Models\Poll;
use Illuminate\Database\Seeder;

class PollSeeder extends Seeder
{
    public function run(): void
    {
        $polls = [
            [
                'title' => 'Meilleur joueur du mois',
                'description' => 'Votez pour le joueur qui a été le plus performant ce mois-ci',
                'options' => [
                    'Attaquant A',
                    'Milieu B',
                    'Défenseur C',
                    'Gardien D',
                ],
                'starts_at' => now(),
                'ends_at' => now()->addDays(7),
                'is_active' => true,
                'allow_multiple' => false,
                'show_results_before_vote' => false,
                'min_user_type' => 'free',
            ],
            [
                'title' => 'Quel design préférez-vous pour le nouveau maillot ?',
                'description' => 'Aidez-nous à choisir le design de la saison prochaine',
                'options' => [
                    'Design classique vert et blanc',
                    'Design moderne avec dégradé',
                    'Design retro années 90',
                ],
                'starts_at' => now(),
                'ends_at' => now()->addDays(14),
                'is_active' => true,
                'allow_multiple' => false,
                'show_results_before_vote' => true,
                'min_user_type' => 'premium',
            ],
            [
                'title' => 'Où devrait-on organiser le prochain événement Socios ?',
                'description' => 'Choix du lieu pour la rencontre avec les joueurs',
                'options' => [
                    'Stade Taïeb Mhiri',
                    'Siège du club',
                    'Hôtel en centre-ville',
                ],
                'starts_at' => now(),
                'ends_at' => now()->addDays(10),
                'is_active' => true,
                'allow_multiple' => false,
                'show_results_before_vote' => false,
                'min_user_type' => 'socios',
            ],
        ];

        foreach ($polls as $poll) {
            Poll::create($poll);
        }

        $this->command->info('Sondages créés avec succès');
    }
}
