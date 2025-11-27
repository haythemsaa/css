<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SociosBenefitRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'benefit_id',
        'user_id',
        'points_spent',
        'redeemed_at',
        'used_at',
        'expires_at',
        'status',
        'redemption_code',
        'notes',
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the benefit that was redeemed
     */
    public function benefit()
    {
        return $this->belongsTo(SociosBenefit::class);
    }

    /**
     * Get the user who redeemed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if redemption is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Mark as used
     */
    public function markAsUsed(): void
    {
        $this->status = 'used';
        $this->used_at = now();
        $this->save();
    }

    /**
     * Scope for user's redemptions
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for unused redemptions
     */
    public function scopeUnused($query)
    {
        return $query->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }
}
