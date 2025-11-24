<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Users & Auth
            UserSeeder::class,

            // Sports
            TeamSeeder::class,
            PlayerSeeder::class,
            MatchSeeder::class,
            TicketSeeder::class,

            // Content
            ContentCategorySeeder::class,
            ContentSeeder::class,

            // Partners
            PartnerCategorySeeder::class,
            PartnerSeeder::class,

            // Donations
            CampaignSeeder::class,

            // Gamification
            BadgeSeeder::class,
            LotteryDrawSeeder::class,
            GiftCampaignSeeder::class,
            CollectibleCardSeeder::class,
            ChallengeSeeder::class,
            LeaderboardSeeder::class,

            // Socios
            SociosBenefitSeeder::class,

            // E-commerce
            ProductSeeder::class,

            // Community
            ForumSeeder::class,
            PollSeeder::class,
        ]);
    }
}
