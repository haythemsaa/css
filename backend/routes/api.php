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

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::get('notifications/preferences', [NotificationController::class, 'preferences']);
        Route::put('notifications/preferences', [NotificationController::class, 'updatePreferences']);
        Route::post('notifications/device-token', [NotificationController::class, 'registerDeviceToken']);

    });
});

// Health check
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});
