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
        'slug',
        'description',
        'image',
        'offer_type',
        'reduction_type',
        'reduction_value',
        'min_purchase_amount',
        'max_discount_amount',
        'valid_from',
        'valid_until',
        'days_of_week',
        'time_slots',
        'conditions',
        'usage_limit_per_user',
        'stock_available',
        'stock_used',
        'status',
        'is_featured',
        'display_order',
        'notification_sent_at',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'time_slots' => 'array',
        'conditions' => 'array',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'reduction_value' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'stock_available' => 'integer',
        'stock_used' => 'integer',
        'usage_limit_per_user' => 'integer',
        'display_order' => 'integer',
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
        return $query->where('status', 'active');
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

    // Helper methods
    public function isValid(): bool
    {
        return $this->status === 'active' &&
               $this->valid_from <= now() &&
               $this->valid_until >= now();
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_used >= $this->stock_available;
    }

    public function isExpired(): bool
    {
        return $this->valid_until < now();
    }

    public function isExpiringSoon(int $days = 7): bool
    {
        return $this->valid_until->diffInDays(now()) <= $days;
    }

    public function getStockRemaining(): int
    {
        return max(0, $this->stock_available - $this->stock_used);
    }

    public function getStockPercentage(): float
    {
        if ($this->stock_available <= 0) {
            return 0;
        }

        return (($this->stock_available - $this->stock_used) / $this->stock_available) * 100;
    }
}
