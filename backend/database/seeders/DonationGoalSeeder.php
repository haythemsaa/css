<?php

namespace Database\Seeders;

use App\Models\DonationGoal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DonationGoalSeeder extends Seeder
{
    public function run(): void
    {
        $goals = [
            [
                'title' => 'Paiement des Litiges Juridiques Urgents',
                'slug' => 'paiement-litiges-juridiques-2024',
                'description' => 'Le club fait face à plusieurs litiges juridiques nécessitant un règlement urgent pour éviter des sanctions',
                'full_details' => "Le Club Sportif Sfaxien doit régler plusieurs litiges juridiques en suspens avec d'anciens joueurs et entraîneurs. Le montant total requis permettra d'éviter des sanctions de la FIFA et de préserver l'honneur du club.",
                'category' => 'litigation',
                'target_amount' => 250000.00,
                'current_amount' => 87500.00,
                'donors_count' => 342,
                'min_donation' => 10.00,
                'priority' => 'urgent',
                'start_date' => now()->subDays(15),
                'end_date' => now()->addDays(30),
                'status' => 'active',
                'is_featured' => true,
                'featured_image' => '/images/goals/litigation.jpg',
                'gallery_images' => ['/images/goals/litigation-1.jpg', '/images/goals/litigation-2.jpg'],
                'milestone_updates' => [
                    ['date' => '2024-01-15', 'message' => '35% atteint! Merci à tous les supporters'],
                    ['date' => '2024-01-20', 'message' => 'Première tranche de paiement effectuée'],
                ],
                'impact_metrics' => '3 litiges sur 5 déjà réglés grâce à vos dons',
                'thank_you_message' => 'Merci infiniment pour votre soutien! Ensemble, nous protégeons l\'avenir du CSS.',
                'show_donors' => true,
                'allow_anonymous' => true,
                'rewards' => [
                    '25%' => 'Badge Bronze Supporter',
                    '50%' => 'Badge Silver Supporter',
                    '75%' => 'Badge Gold Supporter',
                    '100%' => 'Badge Platinum + Certificat d\'Honneur',
                ],
            ],
            [
                'title' => 'Recrutement d\'un Attaquant de Classe Mondiale',
                'slug' => 'recrutement-attaquant-2024',
                'description' => 'Participez au recrutement d\'un attaquant de haut niveau pour renforcer notre équipe',
                'full_details' => "Le CSS a identifié un attaquant de classe mondiale qui pourrait transformer notre jeu offensif. Avec votre aide, nous pouvons finaliser ce transfert historique qui marquera l'histoire du club.",
                'category' => 'player_transfer',
                'target_amount' => 500000.00,
                'current_amount' => 125000.00,
                'donors_count' => 589,
                'min_donation' => 20.00,
                'priority' => 'high',
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(45),
                'status' => 'active',
                'is_featured' => true,
                'featured_image' => '/images/goals/transfer.jpg',
                'gallery_images' => ['/images/goals/transfer-1.jpg'],
                'milestone_updates' => [
                    ['date' => '2024-01-18', 'message' => 'Négociations en cours! 25% collecté'],
                ],
                'impact_metrics' => '+15 buts attendus la saison prochaine',
                'thank_you_message' => 'Vous êtes les véritables acteurs de ce transfert historique!',
                'show_donors' => true,
                'allow_anonymous' => true,
                'rewards' => [
                    '100%' => 'Maillot dédicacé par le nouveau joueur',
                ],
            ],
            [
                'title' => 'Rénovation Tribune Nord - Stade Taïeb Mhiri',
                'slug' => 'renovation-tribune-nord',
                'description' => 'Modernisation complète de la tribune Nord pour améliorer le confort des supporters',
                'full_details' => "La tribune Nord du stade Taïeb Mhiri nécessite une rénovation complète : nouveaux sièges, toiture moderne, système d'éclairage LED, et zones VIP. Un projet d'envergure pour offrir le meilleur à nos fidèles supporters.",
                'category' => 'stadium_renovation',
                'target_amount' => 800000.00,
                'current_amount' => 320000.00,
                'donors_count' => 1247,
                'min_donation' => 10.00,
                'priority' => 'medium',
                'start_date' => now()->subMonth(),
                'end_date' => now()->addMonths(6),
                'status' => 'active',
                'is_featured' => true,
                'featured_image' => '/images/goals/stadium.jpg',
                'gallery_images' => ['/images/goals/stadium-1.jpg', '/images/goals/stadium-2.jpg', '/images/goals/stadium-3.jpg'],
                'milestone_updates' => [
                    ['date' => '2024-01-10', 'message' => '40% atteint! Début des travaux prévu en mars'],
                ],
                'impact_metrics' => '5000 places modernisées, +30% de confort',
                'thank_you_message' => 'Votre nom sera gravé sur le mur des donateurs de la tribune!',
                'show_donors' => true,
                'allow_anonymous' => false,
                'rewards' => [
                    '50%' => 'Visite privée du chantier',
                    '100%' => 'Place VIP inaugurale + Nom sur plaque commémorative',
                ],
            ],
            [
                'title' => 'Développement de l\'Académie CSS - Formation des Jeunes',
                'slug' => 'developpement-academie-jeunes',
                'description' => 'Investissons dans les futures stars du CSS',
                'full_details' => "Notre académie forme les champions de demain. Ces fonds permettront d'améliorer les infrastructures, recruter des formateurs de haut niveau, et offrir des bourses aux jeunes talents défavorisés.",
                'category' => 'youth_academy',
                'target_amount' => 150000.00,
                'current_amount' => 92000.00,
                'donors_count' => 678,
                'min_donation' => 5.00,
                'priority' => 'medium',
                'start_date' => now()->subDays(20),
                'end_date' => null, // Pas de deadline
                'status' => 'active',
                'is_featured' => false,
                'featured_image' => '/images/goals/academy.jpg',
                'gallery_images' => [],
                'milestone_updates' => [
                    ['date' => '2024-01-12', 'message' => '61% collecté! 50 jeunes bénéficiaires'],
                ],
                'impact_metrics' => '100 jeunes formés par an',
                'thank_you_message' => 'Vous investissez dans l\'avenir du CSS!',
                'show_donors' => true,
                'allow_anonymous' => true,
                'rewards' => [],
            ],
            [
                'title' => 'Acquisition d\'Équipements Médicaux Modernes',
                'slug' => 'equipements-medicaux-2024',
                'description' => 'Modernisation du centre médical pour optimiser la récupération des joueurs',
                'full_details' => "Achat d'équipements de pointe : machines de cryothérapie, chambres hyperbares, systèmes d'analyse biomécanique, et matériel de rééducation moderne. Pour réduire les blessures et accélérer la récupération.",
                'category' => 'equipment',
                'target_amount' => 180000.00,
                'current_amount' => 45000.00,
                'donors_count' => 234,
                'min_donation' => 10.00,
                'priority' => 'high',
                'start_date' => now()->subDays(5),
                'end_date' => now()->addMonths(2),
                'status' => 'active',
                'is_featured' => false,
                'featured_image' => '/images/goals/medical.jpg',
                'gallery_images' => ['/images/goals/medical-1.jpg'],
                'milestone_updates' => [],
                'impact_metrics' => '-40% de blessures attendu',
                'thank_you_message' => 'Vous contribuez à la santé de nos joueurs!',
                'show_donors' => true,
                'allow_anonymous' => true,
                'rewards' => [],
            ],
        ];

        foreach ($goals as $goalData) {
            $goal = DonationGoal::create($goalData);

            // Create milestones for each goal
            $milestones = [
                ['percentage' => 25, 'title' => 'Premier Quart', 'target_amount' => $goalData['target_amount'] * 0.25],
                ['percentage' => 50, 'title' => 'Mi-Parcours', 'target_amount' => $goalData['target_amount'] * 0.50],
                ['percentage' => 75, 'title' => 'Trois Quarts', 'target_amount' => $goalData['target_amount'] * 0.75],
                ['percentage' => 100, 'title' => 'Objectif Atteint!', 'target_amount' => $goalData['target_amount']],
            ];

            foreach ($milestones as $index => $milestoneData) {
                $goal->milestones()->create([
                    'title' => $milestoneData['title'],
                    'description' => "Jalon de {$milestoneData['percentage']}% atteint",
                    'target_amount' => $milestoneData['target_amount'],
                    'percentage' => $milestoneData['percentage'],
                    'display_order' => $index + 1,
                ]);
            }

            // Check and update achieved milestones
            $goal->checkMilestones();
        }

        $this->command->info('5 objectifs de dons créés avec jalons');
    }
}
