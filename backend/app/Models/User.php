<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'user_type',
        'profile_photo',
        'date_of_birth',
        'gender',
        'city',
        'country',
        'socios_number',
        'socios_verified',
        'socios_membership_date',
        'socios_card_image',
        'subscription_starts_at',
        'subscription_expires_at',
        'subscription_type',
        'subscription_active',
        'loyalty_points',
        'loyalty_level',
        'facebook_id',
        'google_id',
        'notification_preferences',
        'preferred_language',
        'referral_code',
        'referred_by',
        'referral_count',
        'last_login_at',
        'last_login_ip',
        'is_banned',
        'ban_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
        'socios_verified' => 'boolean',
        'socios_membership_date' => 'date',
        'subscription_starts_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'subscription_active' => 'boolean',
        'notification_preferences' => 'array',
        'last_login_at' => 'datetime',
        'is_banned' => 'boolean',
        'date_of_birth' => 'date',
    ];

    // Relationships
    public function contents(): HasMany
    {
        return $this->hasMany(Content::class, 'author_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function reductionCodes(): HasMany
    {
        return $this->hasMany(ReductionCode::class);
    }

    public function lotteryTickets(): HasMany
    {
        return $this->hasMany(LotteryTicket::class);
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(AchievementBadge::class, 'user_badges', 'user_id', 'badge_id')
            ->withPivot('unlocked_at', 'is_displayed', 'notification_sent')
            ->withTimestamps();
    }

    public function collectibleCards(): BelongsToMany
    {
        return $this->belongsToMany(CollectibleCard::class, 'user_cards', 'user_id', 'card_id')
            ->withPivot('acquired_at', 'acquisition_method', 'is_locked', 'is_favorited')
            ->withTimestamps();
    }

    public function favoritePartners(): BelongsToMany
    {
        return $this->belongsToMany(Partner::class, 'partner_favorites')
            ->withTimestamps();
    }

    public function forumTopics(): HasMany
    {
        return $this->hasMany(ForumTopic::class);
    }

    public function forumReplies(): HasMany
    {
        return $this->hasMany(ForumReply::class);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function pushTokens(): HasMany
    {
        return $this->hasMany(PushNotificationToken::class);
    }

    public function giftDistributions(): HasMany
    {
        return $this->hasMany(GiftDistribution::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function pollVotes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    public function userBadges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(UserCard::class);
    }

    public function cardTradesInitiated(): HasMany
    {
        return $this->hasMany(CardTrade::class, 'initiator_id');
    }

    public function cardTradesReceived(): HasMany
    {
        return $this->hasMany(CardTrade::class, 'recipient_id');
    }

    public function partnerReviews(): HasMany
    {
        return $this->hasMany(PartnerReview::class);
    }

    public function reductionUsages(): HasMany
    {
        return $this->hasMany(ReductionUsage::class);
    }

    public function referredUsers(): HasMany
    {
        return $this->hasMany(ReferralProgram::class, 'referrer_id');
    }

    public function referredBy()
    {
        return $this->hasOne(ReferralProgram::class, 'referred_id');
    }

    public function sociosBenefitRedemptions(): HasMany
    {
        return $this->hasMany(SociosBenefitRedemption::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function leaderboards(): HasMany
    {
        return $this->hasMany(Leaderboard::class);
    }

    public function challenges(): BelongsToMany
    {
        return $this->belongsToMany(Challenge::class, 'user_challenges')
            ->withPivot([
                'current_progress',
                'target_value',
                'progress_percentage',
                'started_at',
                'completed_at',
                'is_completed',
                'reward_claimed',
                'progress_data',
            ])
            ->withTimestamps();
    }

    public function userChallenges(): HasMany
    {
        return $this->hasMany(UserChallenge::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function ticketPurchases(): HasMany
    {
        return $this->hasMany(TicketPurchase::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getIsPremiumAttribute(): bool
    {
        return in_array($this->user_type, ['premium', 'socios']);
    }

    public function getIsSociosAttribute(): bool
    {
        return $this->user_type === 'socios' && $this->socios_verified;
    }

    // Methods
    public function hasActiveSubscription(): bool
    {
        if ($this->user_type === 'socios' && $this->socios_verified) {
            return true; // Socios have lifetime premium
        }

        return $this->subscription_active &&
               $this->subscription_expires_at &&
               $this->subscription_expires_at->isFuture();
    }

    public function addLoyaltyPoints(int $points): void
    {
        $this->loyalty_points += $points;
        $this->updateLoyaltyLevel();
        $this->save();
    }

    public function updateLoyaltyLevel(): void
    {
        $points = $this->loyalty_points;

        if ($points >= 5000) {
            $this->loyalty_level = 'platinum';
        } elseif ($points >= 2500) {
            $this->loyalty_level = 'gold';
        } elseif ($points >= 1000) {
            $this->loyalty_level = 'silver';
        } else {
            $this->loyalty_level = 'bronze';
        }
    }
}
