<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReductionCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'partner_id',
        'offer_id',
        'code',
        'qr_code',
        'type',
        'discount_percentage',
        'max_discount_amount',
        'generated_at',
        'expires_at',
        'is_active',
        'is_used',
        'used_at',
        'original_amount',
        'discount_amount',
        'final_amount',
        'usage_latitude',
        'usage_longitude',
        'location_verified',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'is_active' => 'boolean',
        'is_used' => 'boolean',
        'location_verified' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(PartnerOffer::class, 'offer_id');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(ReductionUsage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('is_used', false)
            ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    // Methods
    public function isValid(): bool
    {
        return $this->is_active
            && !$this->is_used
            && $this->expires_at > now();
    }
}
