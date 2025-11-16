<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Donation;
use App\Models\UserCard;
use App\Models\CollectibleCard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ordre d'exécution des seeders (important pour les foreign keys)
        $this->call([
            PartnerCategorySeeder::class,
            PartnerSeeder::class,
            PartnerOfferSeeder::class,
            PlayerSeeder::class,
            FootballMatchSeeder::class,
            ContentSeeder::class,
            CollectibleCardSeeder::class,
        ]);

        // Créer un utilisateur admin
        User::create([
            'name' => 'Admin CSS',
            'email' => 'admin@css.tn',
            'password' => bcrypt('password'),
            'user_type' => 'socios',
            'socios_verified_at' => now(),
            'loyalty_points' => 5000,
            'last_login_at' => now(),
        ]);

        // Créer 30 utilisateurs Free
        User::factory(30)->create([
            'user_type' => 'free',
            'loyalty_points' => fake()->numberBetween(0, 500),
        ]);

        // Créer 40 utilisateurs Premium
        User::factory(40)->create([
            'user_type' => 'premium',
            'subscription_expires_at' => now()->addMonths(fake()->numberBetween(1, 12)),
            'loyalty_points' => fake()->numberBetween(500, 2000),
        ]);

        // Créer 30 utilisateurs Socios
        for ($i = 1; $i <= 30; $i++) {
            User::factory()->create([
                'user_type' => 'socios',
                'socios_verified_at' => now()->subDays(rand(1, 365)),
                'socios_number' => 'CSS-' . str_pad($i + 100000, 6, '0', STR_PAD_LEFT),
                'loyalty_points' => rand(1000, 10000),
            ]);
        }

        // Créer quelques donations
        $users = User::all();
        for ($i = 0; $i < 50; $i++) {
            Donation::create([
                'user_id' => $users->random()->id,
                'amount' => fake()->randomFloat(2, 10, 500),
                'type' => fake()->randomElement(['libre', 'ciblé']),
                'status' => fake()->randomElement(['pending', 'completed', 'completed', 'completed']),
                'payment_method' => fake()->randomElement(['credit_card', 'mobile_money', 'bank_transfer']),
                'transaction_id' => 'TXN-' . fake()->unique()->numerify('##########'),
                'message' => fake()->boolean(30) ? fake()->sentence() : null,
                'is_anonymous' => fake()->boolean(20),
                'created_at' => now()->subDays(rand(0, 180)),
            ]);
        }

        // Distribuer des cartes à collectionner aux utilisateurs
        $cards = CollectibleCard::all();
        $eligibleUsers = User::where('user_type', '!=', 'free')
            ->orWhere('loyalty_points', '>', 100)
            ->limit(60)
            ->get();

        foreach ($eligibleUsers as $user) {
            // Chaque utilisateur reçoit entre 3 et 20 cartes
            $numCards = rand(3, 20);
            $userCards = $cards->random(min($numCards, $cards->count()));

            foreach ($userCards as $card) {
                UserCard::create([
                    'user_id' => $user->id,
                    'card_id' => $card->id,
                    'acquired_at' => now()->subDays(rand(1, 180)),
                    'source' => fake()->randomElement(['pack', 'trade', 'gift', 'purchase']),
                ]);
            }
        }

        $this->command->info('Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('Summary:');
        $this->command->info('- 8 Partner Categories');
        $this->command->info('- ' . \App\Models\Partner::count() . ' Partners');
        $this->command->info('- ' . \App\Models\PartnerOffer::count() . ' Partner Offers');
        $this->command->info('- ' . \App\Models\Player::count() . ' Players');
        $this->command->info('- ' . \App\Models\FootballMatch::count() . ' Football Matches');
        $this->command->info('- ' . \App\Models\Content::count() . ' Content Items');
        $this->command->info('- ' . \App\Models\CollectibleCard::count() . ' Collectible Cards');
        $this->command->info('- ' . User::count() . ' Users (101 total)');
        $this->command->info('- ' . Donation::count() . ' Donations');
        $this->command->info('- ' . UserCard::count() . ' User Cards distributed');
        $this->command->info('');
        $this->command->info('Test credentials:');
        $this->command->info('Email: admin@css.tn');
        $this->command->info('Password: password');
    }
}
