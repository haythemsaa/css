<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'description',
        'logo',
        'cover_image',
        'phone',
        'email',
        'website',
        'address',
        'city',
        'postal_code',
        'latitude',
        'longitude',
        'reduction_value_premium',
        'reduction_value_socios',
        'reduction_type',
        'opening_hours',
        'special_conditions',
        'commission_percentage',
        'capacity_daily',
        'capacity_per_user',
        'max_discount_amount',
        'is_active',
        'is_featured',
        'priority',
        'contract_starts_at',
        'contract_ends_at',
        'total_uses',
        'total_revenue',
        'total_commission',
        'average_rating',
        'reviews_count',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'contract_starts_at' => 'date',
        'contract_ends_at' => 'date',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(PartnerCategory::class, 'category_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(PartnerOffer::class);
    }

    public function reductionCodes(): HasMany
    {
        return $this->hasMany(ReductionCode::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(PartnerReview::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'partner_favorites')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeNearby($query, $latitude, $longitude, $radius = 5)
    {
        // Calculate distance in km using Haversine formula
        return $query->selectRaw(
            "*, ( 6371 * acos( cos( radians(?) ) *
            cos( radians( latitude ) ) *
            cos( radians( longitude ) - radians(?) ) +
            sin( radians(?) ) *
            sin( radians( latitude ) ) ) ) AS distance",
            [$latitude, $longitude, $latitude]
        )
        ->having('distance', '<', $radius)
        ->orderBy('distance');
    }

    // Methods
    public function getDiscountForUser(User $user): float
    {
        if ($user->user_type === 'socios' && $user->socios_verified) {
            return $this->reduction_value_socios;
        }

        if ($user->user_type === 'premium') {
            return $this->reduction_value_premium;
        }

        return 0;
    }

    public function calculateDiscountAmount(float $originalAmount, User $user): float
    {
        $discount = $this->getDiscountForUser($user);

        if ($this->reduction_type === 'percentage') {
            $discountAmount = ($originalAmount * $discount) / 100;
        } else {
            $discountAmount = $discount;
        }

        if ($this->max_discount_amount && $discountAmount > $this->max_discount_amount) {
            $discountAmount = $this->max_discount_amount;
        }

        return $discountAmount;
    }

    public function hasReachedDailyCapacity(): bool
    {
        if (!$this->capacity_daily) {
            return false;
        }

        $todayUses = $this->reductionCodes()
            ->where('is_used', true)
            ->whereDate('used_at', today())
            ->count();

        return $todayUses >= $this->capacity_daily;
    }
}
