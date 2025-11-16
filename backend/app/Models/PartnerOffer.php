<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnerOffer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'partner_id',
        'title',
        'description',
        'offer_type',
        'reduction_type',
        'reduction_value',
        'min_purchase_amount',
        'max_discount_amount',
        'valid_from',
        'valid_until',
        'days_of_week',
        'time_slots',
        'images',
        'stock_available',
        'stock_used',
        'user_limit_per_day',
        'user_limit_per_month',
        'is_active',
        'is_featured',
        'notification_sent_at',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'time_slots' => 'array',
        'images' => 'array',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'reduction_value' => 'float',
        'min_purchase_amount' => 'float',
        'max_discount_amount' => 'float',
        'stock_available' => 'integer',
        'stock_used' => 'integer',
        'user_limit_per_day' => 'integer',
        'user_limit_per_month' => 'integer',
        'is_featured' => 'boolean',
        'notification_sent_at' => 'datetime',
    ];

    // Relations
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function reductionCodes(): HasMany
    {
        return $this->hasMany(ReductionCode::class, 'offer_id');
    }

    public function reductionUsages(): HasMany
    {
        return $this->hasMany(ReductionUsage::class, 'offer_id');
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

    public function scopeValid($query)
    {
        return $query->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now());
    }
}
