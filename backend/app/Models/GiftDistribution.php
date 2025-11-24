<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'user_id',
        'gift_type',
        'gift_value',
        'claimed_at',
        'expires_at',
        'is_claimed',
        'redemption_code',
        'notes',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_claimed' => 'boolean',
    ];

    /**
     * Get the campaign this distribution belongs to
     */
    public function campaign()
    {
        return $this->belongsTo(GiftCampaign::class);
    }

    /**
     * Get the user who received this gift
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if gift is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if gift is available for claiming
     */
    public function isAvailable(): bool
    {
        return !$this->is_claimed && !$this->isExpired();
    }

    /**
     * Mark gift as claimed
     */
    public function claim(): void
    {
        $this->is_claimed = true;
        $this->claimed_at = now();
        $this->save();
    }

    /**
     * Scope for unclaimed gifts
     */
    public function scopeUnclaimed($query)
    {
        return $query->where('is_claimed', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope for user's gifts
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
