<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'name',
        'slug',
        'description',
        'logo',
        'cover_image',
        'address',
        'city',
        'postal_code',
        'phone',
        'email',
        'website',
        'latitude',
        'longitude',
        'reduction_type',
        'reduction_value_premium',
        'reduction_value_socios',
        'conditions',
        'exclusions',
        'opening_hours',
        'is_online',
        'is_geolocation_enabled',
        'commission_percentage',
        'capacity_daily',
        'capacity_weekly',
        'views_count',
        'reviews_count',
        'rating_average',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'conditions' => 'array',
        'exclusions' => 'array',
        'opening_hours' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'reduction_value_premium' => 'float',
        'reduction_value_socios' => 'float',
        'commission_percentage' => 'float',
        'is_online' => 'boolean',
        'is_geolocation_enabled' => 'boolean',
        'capacity_daily' => 'integer',
        'capacity_weekly' => 'integer',
        'views_count' => 'integer',
        'reviews_count' => 'integer',
        'rating_average' => 'float',
    ];

    // Relations
    public function category(): BelongsTo
    {
        return $this->belongsTo(PartnerCategory::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(PartnerCategory::class, 'subcategory_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(PartnerOffer::class);
    }

    public function reductionCodes(): HasMany
    {
        return $this->hasMany(ReductionCode::class);
    }

    public function reductionUsages(): HasMany
    {
        return $this->hasMany(ReductionUsage::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(PartnerReview::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNearby($query, $lat, $lng, $radius = 10)
    {
        // Haversine formula for distance calculation
        return $query->whereRaw("
            (6371 * acos(cos(radians(?))
            * cos(radians(latitude))
            * cos(radians(longitude) - radians(?))
            + sin(radians(?))
            * sin(radians(latitude)))) <= ?
        ", [$lat, $lng, $lat, $radius]);
    }
}
