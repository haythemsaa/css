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
            // Admin Users (doit être en premier)
            AdminUserSeeder::class,

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
            SubscriptionTierSeeder::class,
            DiscountCodeSeeder::class,

            // Payment Methods
            PaymentMethodSeeder::class,

            // Events
            EventSeeder::class,

            // Auctions
            AuctionSeeder::class,

            // Donation Goals
            DonationGoalSeeder::class,

            // Community
            ForumSeeder::class,
            PollSeeder::class,
        ]);
    }
}
