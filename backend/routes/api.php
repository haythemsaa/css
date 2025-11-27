<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\ReductionCodeController;
use App\Http\Controllers\Api\GiftController;
use App\Http\Controllers\Api\LotteryController;
use App\Http\Controllers\Api\CollectibleCardController;
use App\Http\Controllers\Api\BadgeController;
use App\Http\Controllers\Api\SociosController;
use App\Http\Controllers\Api\ForumController;
use App\Http\Controllers\Api\PollController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\ChallengeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\SociosBenefitController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\SupportTicketController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SocialController;
use App\Http\Controllers\Api\AuctionController;
use App\Http\Controllers\Api\DonationGoalController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\TicketMarketplaceController;
use App\Http\Controllers\Api\FanTokenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ===================================
    // PUBLIC ROUTES (No Authentication)
    // ===================================

    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('resend-otp', [AuthController::class, 'resendOtp']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
        Route::post('social-login/{provider}', [AuthController::class, 'socialLogin']);
    });

    // Public content (free tier)
    Route::get('contents', [ContentController::class, 'index']);
    Route::get('contents/{slug}', [ContentController::class, 'show']);
    Route::get('contents/featured', [ContentController::class, 'featured']);
    Route::get('contents/trending', [ContentController::class, 'trending']);

    // Public matches
    Route::get('matches', [MatchController::class, 'index']);
    Route::get('matches/{id}', [MatchController::class, 'show']);
    Route::get('matches/{id}/live', [MatchController::class, 'live']);
    Route::get('standings', [MatchController::class, 'standings']);

    // Public players
    Route::get('players', [PlayerController::class, 'index']);
    Route::get('players/{id}', [PlayerController::class, 'show']);

    // Public campaigns
    Route::get('campaigns', [CampaignController::class, 'index']);
    Route::get('campaigns/{slug}', [CampaignController::class, 'show']);

    // Public partners (browsing)
    Route::get('partners', [PartnerController::class, 'index']);
    Route::get('partners/{id}', [PartnerController::class, 'show']);
    Route::get('partners/categories', [PartnerController::class, 'categories']);
    Route::get('partners/nearby', [PartnerController::class, 'nearby']);

    // Public teams
    Route::get('teams', [TeamController::class, 'index']);
    Route::get('teams/{id}', [TeamController::class, 'show']);

    // Public leaderboards
    Route::get('leaderboards', [LeaderboardController::class, 'index']);
    Route::get('leaderboards/stats', [LeaderboardController::class, 'stats']);

    // Public challenges
    Route::get('challenges', [ChallengeController::class, 'index']);
    Route::get('challenges/{id}', [ChallengeController::class, 'show']);

    // Public products (E-commerce)
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::get('products/{id}/reviews', [ProductController::class, 'reviews']);

    // Public tickets for matches
    Route::get('matches/{matchId}/tickets', [TicketController::class, 'index']);

    // Ticket verification (for gate staff)
    Route::post('tickets/verify-qr', [TicketController::class, 'verifyQRCode']);
    Route::post('tickets/mark-used', [TicketController::class, 'markAsUsed']);

    // Public events
    Route::get('events', [EventController::class, 'index']);
    Route::get('events/{id}', [EventController::class, 'show']);

    // Public search
    Route::get('search', [SearchController::class, 'search']);
    Route::get('search/suggestions', [SearchController::class, 'suggestions']);
    Route::get('search/trending', [SearchController::class, 'trending']);

    // Public auctions
    Route::get('auctions', [AuctionController::class, 'index']);
    Route::get('auctions/{id}', [AuctionController::class, 'show']);

    // Public donation goals
    Route::get('donation-goals', [DonationGoalController::class, 'index']);
    Route::get('donation-goals/{slug}', [DonationGoalController::class, 'show']);
    Route::get('donation-goals/categories', [DonationGoalController::class, 'categories']);

    // Public payment methods
    Route::get('payment-methods', [PaymentMethodController::class, 'index']);
    Route::get('payment-methods/{id}', [PaymentMethodController::class, 'show']);
    Route::post('payment-methods/{id}/calculate-fees', [PaymentMethodController::class, 'calculateFees']);

    // ===================================
    // AUTHENTICATED ROUTES
    // ===================================

    Route::middleware('auth:sanctum')->group(function () {

        // Auth User
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('user/profile', [AuthController::class, 'profile']);
        Route::put('user/profile', [AuthController::class, 'updateProfile']);
        Route::post('user/change-password', [AuthController::class, 'changePassword']);
        Route::delete('user/account', [AuthController::class, 'deleteAccount']);

        // Content interaction (Premium/Socios)
        Route::post('contents/{id}/like', [ContentController::class, 'like']);
        Route::post('contents/{id}/unlike', [ContentController::class, 'unlike']);
        Route::post('contents/{id}/share', [ContentController::class, 'share']);
        Route::get('videos/{id}/stream', [ContentController::class, 'streamVideo']);

        // Match predictions
        Route::get('matches/{id}/predict', [MatchController::class, 'predict']);
        Route::post('matches/{id}/prediction', [MatchController::class, 'storePrediction']);

        // Player stats (Premium)
        Route::get('players/{id}/statistics', [PlayerController::class, 'statistics']);
        Route::get('players/{id}/videos', [PlayerController::class, 'videos']);

        // Donations
        Route::post('donations', [DonationController::class, 'store']);
        Route::get('donations/history', [DonationController::class, 'history']);
        Route::get('donations/stats', [DonationController::class, 'stats']);
        Route::get('donations/{id}/certificate', [DonationController::class, 'certificate']);

        // Socios
        Route::prefix('socios')->middleware('role:socios')->group(function () {
            Route::post('verify', [SociosController::class, 'verify']);
            Route::get('benefits', [SociosController::class, 'benefits']);
            Route::post('benefits/{id}/redeem', [SociosController::class, 'redeemBenefit']);
            Route::get('events', [SociosController::class, 'events']);
            Route::get('points-history', [SociosController::class, 'pointsHistory']);
        });

        // Partners & Offers (Premium/Socios only)
        Route::middleware('role:premium|socios')->group(function () {
            Route::post('partners/{id}/favorite', [PartnerController::class, 'favorite']);
            Route::delete('partners/{id}/unfavorite', [PartnerController::class, 'unfavorite']);
            Route::get('partners/favorites', [PartnerController::class, 'favorites']);
            Route::post('partners/{id}/review', [PartnerController::class, 'review']);

            // Offers
            Route::get('offers', [OfferController::class, 'index']);
            Route::get('offers/{id}', [OfferController::class, 'show']);
            Route::get('offers/flash', [OfferController::class, 'flash']);
            Route::get('offers/featured', [OfferController::class, 'featured']);

            // Reduction codes
            Route::post('reductions/generate', [ReductionCodeController::class, 'generate']);
            Route::get('reductions/active', [ReductionCodeController::class, 'active']);
            Route::get('reductions/history', [ReductionCodeController::class, 'history']);
            Route::post('reductions/{code}/validate', [ReductionCodeController::class, 'validate']);
            Route::post('reductions/{id}/rate', [ReductionCodeController::class, 'rate']);
            Route::get('reductions/stats', [ReductionCodeController::class, 'stats']);
        });

        // Gifts
        Route::get('gifts/available', [GiftController::class, 'available']);
        Route::get('gifts/my-gifts', [GiftController::class, 'myGifts']);
        Route::post('gifts/{id}/claim', [GiftController::class, 'claim']);
        Route::get('gifts/calendar', [GiftController::class, 'calendar']);

        // Lottery
        Route::get('lottery/active', [LotteryController::class, 'active']);
        Route::get('lottery/{id}', [LotteryController::class, 'show']);
        Route::post('lottery/{id}/buy-ticket', [LotteryController::class, 'buyTicket']);
        Route::get('lottery/my-tickets', [LotteryController::class, 'myTickets']);
        Route::get('lottery/{id}/winners', [LotteryController::class, 'winners']);

        // Collectible Cards
        Route::get('cards/available', [CollectibleCardController::class, 'available']);
        Route::get('cards/my-collection', [CollectibleCardController::class, 'myCollection']);
        Route::post('cards/{id}/acquire', [CollectibleCardController::class, 'acquire']);
        Route::get('cards/trade-offers', [CollectibleCardController::class, 'tradeOffers']);
        Route::post('cards/trade', [CollectibleCardController::class, 'proposeTrade']);
        Route::post('cards/trade/{id}/accept', [CollectibleCardController::class, 'acceptTrade']);
        Route::post('cards/trade/{id}/reject', [CollectibleCardController::class, 'rejectTrade']);

        // Badges
        Route::get('badges/all', [BadgeController::class, 'all']);
        Route::get('badges/my-badges', [BadgeController::class, 'myBadges']);
        Route::get('badges/{id}/progress', [BadgeController::class, 'progress']);

        // Referral
        Route::get('referral/my-code', [ReferralController::class, 'myCode']);
        Route::post('referral/invite', [ReferralController::class, 'invite']);
        Route::get('referral/stats', [ReferralController::class, 'stats']);
        Route::get('referral/rewards', [ReferralController::class, 'rewards']);

        // Forum
        Route::prefix('forum')->group(function () {
            Route::get('categories', [ForumController::class, 'categories']);
            Route::get('topics', [ForumController::class, 'topics']);
            Route::get('topics/{id}', [ForumController::class, 'showTopic']);
            Route::post('topics', [ForumController::class, 'createTopic']);
            Route::put('topics/{id}', [ForumController::class, 'updateTopic']);
            Route::delete('topics/{id}', [ForumController::class, 'deleteTopic']);
            Route::post('topics/{id}/reply', [ForumController::class, 'reply']);
            Route::put('replies/{id}', [ForumController::class, 'updateReply']);
            Route::delete('replies/{id}', [ForumController::class, 'deleteReply']);
            Route::post('topics/{id}/like', [ForumController::class, 'likeTopic']);
            Route::post('replies/{id}/like', [ForumController::class, 'likeReply']);
        });

        // Polls
        Route::get('polls', [PollController::class, 'index']);
        Route::get('polls/{id}', [PollController::class, 'show']);
        Route::post('polls/{id}/vote', [PollController::class, 'vote']);
        Route::get('polls/{id}/results', [PollController::class, 'results']);

        // Ticket Marketplace
        Route::prefix('marketplace')->group(function () {
            Route::get('listings', [TicketMarketplaceController::class, 'index']);
            Route::get('listings/{id}', [TicketMarketplaceController::class, 'show']);
            Route::post('listings', [TicketMarketplaceController::class, 'store']);
            Route::put('listings/{id}', [TicketMarketplaceController::class, 'update']);
            Route::post('listings/{id}/reserve', [TicketMarketplaceController::class, 'reserve']);
            Route::post('listings/{id}/purchase', [TicketMarketplaceController::class, 'purchase']);
            Route::delete('listings/{id}/cancel', [TicketMarketplaceController::class, 'cancel']);
            Route::get('my-listings', [TicketMarketplaceController::class, 'myListings']);
            Route::get('my-purchases', [TicketMarketplaceController::class, 'myPurchases']);
            Route::post('transactions/{id}/review', [TicketMarketplaceController::class, 'submitReview']);
            Route::get('users/{userId}/reviews', [TicketMarketplaceController::class, 'getUserReviews']);
        });

        // Fan Tokens & Rewards
        Route::prefix('tokens')->group(function () {
            Route::get('wallet', [FanTokenController::class, 'getWallet']);
            Route::get('transactions', [FanTokenController::class, 'getTransactions']);
            Route::post('daily-bonus', [FanTokenController::class, 'claimDailyBonus']);
            Route::get('leaderboard', [FanTokenController::class, 'getLeaderboard']);
        });

        Route::prefix('rewards')->group(function () {
            Route::get('/', [FanTokenController::class, 'getRewards']);
            Route::get('/{id}', [FanTokenController::class, 'getRewardDetails']);
            Route::post('/{id}/redeem', [FanTokenController::class, 'redeemReward']);
        });

        Route::prefix('redemptions')->group(function () {
            Route::get('/', [FanTokenController::class, 'getMyRedemptions']);
            Route::get('/{id}', [FanTokenController::class, 'getRedemptionDetails']);
            Route::delete('/{id}', [FanTokenController::class, 'cancelRedemption']);
        });

        // Badges & Achievements
        Route::prefix('badges')->group(function () {
            Route::get('/', [BadgeController::class, 'index']);
            Route::get('/summary', [BadgeController::class, 'getUserSummary']);
            Route::get('/categories', [BadgeController::class, 'getByCategory']);
            Route::get('/leaderboard', [BadgeController::class, 'getLeaderboard']);
            Route::get('/{id}', [BadgeController::class, 'show']);
            Route::post('/{id}/unlock', [BadgeController::class, 'unlockBadge']);
            Route::post('/{id}/progress', [BadgeController::class, 'updateProgress']);
        });

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::get('notifications/preferences', [NotificationController::class, 'preferences']);
        Route::put('notifications/preferences', [NotificationController::class, 'updatePreferences']);
        Route::post('notifications/device-token', [NotificationController::class, 'registerDeviceToken']);

        // Leaderboards (User-specific)
        Route::get('leaderboards/my-rank', [LeaderboardController::class, 'userRank']);
        Route::get('leaderboards/compare/{userId}', [LeaderboardController::class, 'compare']);

        // Challenges (User actions)
        Route::post('challenges/{id}/enroll', [ChallengeController::class, 'enroll']);
        Route::get('challenges/my-challenges', [ChallengeController::class, 'userChallenges']);
        Route::patch('challenges/progress/{userChallengeId}', [ChallengeController::class, 'updateProgress']);
        Route::post('challenges/claim/{userChallengeId}', [ChallengeController::class, 'claimReward']);

        // E-commerce - Products (Reviews)
        Route::post('products/{id}/reviews', [ProductController::class, 'addReview']);

        // E-commerce - Cart
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'show']);
            Route::post('/items', [CartController::class, 'addItem']);
            Route::patch('/items/{productId}', [CartController::class, 'updateItem']);
            Route::delete('/items/{productId}', [CartController::class, 'removeItem']);
            Route::delete('/', [CartController::class, 'clear']);
        });

        // E-commerce - Wishlist
        Route::prefix('wishlist')->group(function () {
            Route::get('/', [WishlistController::class, 'index']);
            Route::post('/', [WishlistController::class, 'store']);
            Route::post('/toggle', [WishlistController::class, 'toggle']);
            Route::delete('/{productId}', [WishlistController::class, 'destroy']);
            Route::get('/check/{productId}', [WishlistController::class, 'check']);
        });

        // E-commerce - Orders
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);
            Route::get('/{id}', [OrderController::class, 'show']);
            Route::delete('/{id}/cancel', [OrderController::class, 'cancel']);
        });

        // Ticketing
        Route::prefix('tickets')->group(function () {
            Route::post('/purchase', [TicketController::class, 'purchase']);
            Route::get('/my-tickets', [TicketController::class, 'myTickets']);
            Route::get('/purchases/{id}', [TicketController::class, 'show']);
            Route::delete('/purchases/{id}/cancel', [TicketController::class, 'cancel']);
        });

        // Socios Benefits & Subscriptions
        Route::prefix('socios-benefits')->group(function () {
            Route::post('/{id}/redeem', [SociosBenefitController::class, 'redeem']);
        });

        Route::prefix('subscriptions')->group(function () {
            Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
            Route::get('/my-subscription', [SubscriptionController::class, 'mySubscription']);
        });

        // Events (Authenticated)
        Route::prefix('events')->group(function () {
            Route::post('/{id}/register', [EventController::class, 'register']);
            Route::delete('/registrations/{id}', [EventController::class, 'cancelRegistration']);
            Route::get('/my-registrations', [EventController::class, 'myRegistrations']);
            Route::post('/verify-qr', [EventController::class, 'verifyQRCode']);
        });

        // Support Tickets
        Route::prefix('support')->group(function () {
            Route::get('/tickets', [SupportTicketController::class, 'index']);
            Route::post('/tickets', [SupportTicketController::class, 'store']);
            Route::get('/tickets/{id}', [SupportTicketController::class, 'show']);
            Route::post('/tickets/{id}/messages', [SupportTicketController::class, 'addMessage']);
            Route::patch('/tickets/{id}/rate', [SupportTicketController::class, 'rateSatisfaction']);
        });

        // Social Features
        Route::prefix('social')->group(function () {
            // Follow System
            Route::post('/follow/{userId}', [SocialController::class, 'follow']);
            Route::delete('/unfollow/{userId}', [SocialController::class, 'unfollow']);
            Route::get('/followers', [SocialController::class, 'followers']);
            Route::get('/following', [SocialController::class, 'following']);

            // Timeline Posts
            Route::get('/timeline', [SocialController::class, 'timeline']);
            Route::post('/posts', [SocialController::class, 'createPost']);
            Route::delete('/posts/{id}', [SocialController::class, 'deletePost']);
            Route::post('/posts/{id}/like', [SocialController::class, 'likePost']);
            Route::delete('/posts/{id}/unlike', [SocialController::class, 'unlikePost']);
            Route::post('/posts/{id}/comments', [SocialController::class, 'addComment']);
            Route::delete('/comments/{id}', [SocialController::class, 'deleteComment']);

            // Wishlist
            Route::get('/wishlist', [SocialController::class, 'wishlist']);
            Route::post('/wishlist/{productId}', [SocialController::class, 'addToWishlist']);
            Route::delete('/wishlist/{productId}', [SocialController::class, 'removeFromWishlist']);

            // Saved Content
            Route::get('/saved', [SocialController::class, 'savedContent']);
            Route::post('/saved/{contentId}', [SocialController::class, 'saveContent']);
            Route::delete('/saved/{contentId}', [SocialController::class, 'unsaveContent']);
        });

        // Auctions (Authenticated)
        Route::prefix('auctions')->group(function () {
            Route::post('/{id}/bid', [AuctionController::class, 'placeBid']);
            Route::post('/{id}/buy-now', [AuctionController::class, 'buyNow']);
            Route::get('/my-bids', [AuctionController::class, 'myBids']);
            Route::get('/my-wins', [AuctionController::class, 'myWins']);
        });

        // Donation Goals (Authenticated)
        Route::prefix('donation-goals')->group(function () {
            Route::post('/{id}/donate', [DonationGoalController::class, 'donate']);
            Route::get('/my-donations', [DonationGoalController::class, 'myDonations']);
        });

    });

    // ===================================
    // ADMIN ROUTES
    // ===================================

    Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {

        // ===================================
        // DASHBOARD & STATISTICS
        // ===================================
        Route::get('dashboard/stats', function () {
            return response()->json([
                'users' => [
                    'total' => \App\Models\User::count(),
                    'socios' => \App\Models\User::where('user_type', 'socios')->count(),
                    'premium' => \App\Models\User::where('user_type', 'premium')->count(),
                    'active_today' => \App\Models\User::whereDate('last_login_at', today())->count(),
                ],
                'auctions' => [
                    'total' => \App\Models\AuctionProduct::count(),
                    'active' => \App\Models\AuctionProduct::active()->count(),
                    'revenue' => \App\Models\AuctionProduct::where('status', 'sold')->sum('current_bid'),
                ],
                'donations' => [
                    'total_goals' => \App\Models\DonationGoal::count(),
                    'active_goals' => \App\Models\DonationGoal::active()->count(),
                    'total_raised' => \App\Models\DonationGoal::sum('current_amount'),
                ],
                'payments' => [
                    'total_methods' => \App\Models\PaymentMethod::count(),
                    'active_methods' => \App\Models\PaymentMethod::active()->count(),
                    'total_transactions' => \App\Models\PaymentTransaction::count(),
                ],
                'commerce' => [
                    'total_products' => \App\Models\Product::count(),
                    'total_orders' => \App\Models\Order::count(),
                    'revenue' => \App\Models\Order::where('status', 'completed')->sum('total_amount'),
                ],
            ]);
        });

        // ===================================
        // AUCTIONS MANAGEMENT
        // ===================================
        Route::prefix('auctions')->group(function () {
            Route::post('/', [AuctionController::class, 'store']);
            Route::put('/{id}', [AuctionController::class, 'update']);
            Route::delete('/{id}', [AuctionController::class, 'destroy']);
            Route::get('/statistics', [AuctionController::class, 'statistics']);
        });

        // ===================================
        // DONATION GOALS MANAGEMENT
        // ===================================
        Route::prefix('donation-goals')->group(function () {
            Route::post('/', [DonationGoalController::class, 'store']);
            Route::put('/{id}', [DonationGoalController::class, 'update']);
            Route::delete('/{id}', [DonationGoalController::class, 'destroy']);
            Route::get('/statistics', [DonationGoalController::class, 'statistics']);
        });

        // ===================================
        // PAYMENT METHODS MANAGEMENT
        // ===================================
        Route::prefix('payment-methods')->group(function () {
            Route::post('/', [PaymentMethodController::class, 'store']);
            Route::put('/{id}', [PaymentMethodController::class, 'update']);
            Route::delete('/{id}', [PaymentMethodController::class, 'destroy']);
            Route::get('/statistics', [PaymentMethodController::class, 'statistics']);
        });

        // ===================================
        // CONTENT MANAGEMENT
        // ===================================
        Route::prefix('content')->group(function () {
            Route::get('/', [ContentController::class, 'index']);
            Route::post('/', [ContentController::class, 'index']); // AdminStore method to be added
            Route::put('/{id}', [ContentController::class, 'index']); // AdminUpdate method to be added
            Route::delete('/{id}', [ContentController::class, 'index']); // AdminDestroy method to be added
        });

        // ===================================
        // MATCH MANAGEMENT
        // ===================================
        Route::prefix('matches')->group(function () {
            Route::get('/', [MatchController::class, 'index']);
            Route::post('/', [MatchController::class, 'index']); // AdminStore to be added
            Route::put('/{id}', [MatchController::class, 'index']); // AdminUpdate to be added
            Route::delete('/{id}', [MatchController::class, 'index']); // AdminDestroy to be added
        });

        // ===================================
        // PLAYER MANAGEMENT
        // ===================================
        Route::prefix('players')->group(function () {
            Route::get('/', [PlayerController::class, 'index']);
            Route::post('/', [PlayerController::class, 'index']); // AdminStore to be added
            Route::put('/{id}', [PlayerController::class, 'index']); // AdminUpdate to be added
            Route::delete('/{id}', [PlayerController::class, 'index']); // AdminDestroy to be added
        });

        // ===================================
        // PRODUCT MANAGEMENT
        // ===================================
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::post('/', [ProductController::class, 'index']); // AdminStore to be added
            Route::put('/{id}', [ProductController::class, 'index']); // AdminUpdate to be added
            Route::delete('/{id}', [ProductController::class, 'index']); // AdminDestroy to be added
        });

        // ===================================
        // EVENT MANAGEMENT
        // ===================================
        Route::prefix('events')->group(function () {
            Route::get('/', [EventController::class, 'index']);
            Route::post('/', [EventController::class, 'index']); // AdminStore to be added
            Route::put('/{id}', [EventController::class, 'index']); // AdminUpdate to be added
            Route::delete('/{id}', [EventController::class, 'index']); // AdminDestroy to be added
        });

        // ===================================
        // PARTNER & OFFER MANAGEMENT
        // ===================================
        Route::prefix('partners')->group(function () {
            Route::get('/', [PartnerController::class, 'index']);
            Route::post('/', [PartnerController::class, 'index']); // AdminStore to be added
            Route::put('/{id}', [PartnerController::class, 'index']); // AdminUpdate to be added
            Route::delete('/{id}', [PartnerController::class, 'index']); // AdminDestroy to be added
        });

        // ===================================
        // LOTTERY MANAGEMENT
        // ===================================
        Route::prefix('lottery')->group(function () {
            Route::get('/', [LotteryController::class, 'index']);
            Route::post('/', [LotteryController::class, 'index']); // AdminStore to be added
            Route::put('/{id}', [LotteryController::class, 'index']); // AdminUpdate to be added
            Route::delete('/{id}', [LotteryController::class, 'index']); // AdminDestroy to be added
        });

        // ===================================
        // POLL MANAGEMENT
        // ===================================
        Route::prefix('polls')->group(function () {
            Route::get('/', [PollController::class, 'index']);
            Route::post('/', [PollController::class, 'index']); // AdminStore to be added
            Route::put('/{id}', [PollController::class, 'index']); // AdminUpdate to be added
            Route::delete('/{id}', [PollController::class, 'index']); // AdminDestroy to be added
        });

    });
});

// Health check
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});
