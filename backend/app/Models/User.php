<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'birth_date',
        'address',
        'city',
        'postal_code',
        'country',
        'profile_photo',
        'account_type',
        'socios_number',
        'socios_verified_at',
        'subscription_expires_at',
        'loyalty_points',
        'preferences',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'array',
            'birth_date' => 'date',
            'socios_verified_at' => 'datetime',
            'subscription_expires_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    // Relations
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

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

    public function reductionUsages(): HasMany
    {
        return $this->hasMany(ReductionUsage::class);
    }

    public function partnerReviews(): HasMany
    {
        return $this->hasMany(PartnerReview::class);
    }

    public function giftDistributions(): HasMany
    {
        return $this->hasMany(GiftDistribution::class);
    }

    public function loyaltyTransactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function lotteryTickets(): HasMany
    {
        return $this->hasMany(LotteryTicket::class);
    }

    public function userCards(): HasMany
    {
        return $this->hasMany(UserCard::class);
    }

    public function userBadges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }

    // Scopes
    public function scopeFree($query)
    {
        return $query->where('account_type', 'free');
    }

    public function scopePremium($query)
    {
        return $query->where('account_type', 'premium');
    }

    public function scopeSocios($query)
    {
        return $query->where('account_type', 'socios');
    }

    public function scopeActive($query)
    {
        return $query->whereNotNull('last_login_at')
            ->where('last_login_at', '>=', now()->subMonths(3));
    }

    public function scopeSociosVerified($query)
    {
        return $query->whereNotNull('socios_verified_at');
    }

    // Filament
    public function canAccessPanel(Panel $panel): bool
    {
        // Allow only verified Socios users or specific admin emails
        return $this->email === 'admin@css.tn'
            || ($this->user_type === 'socios' && $this->socios_verified);
    }
}
