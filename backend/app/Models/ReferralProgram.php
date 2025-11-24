<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReferralProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referred_id',
        'referral_code',
        'status',
        'referrer_reward',
        'referred_reward',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($referral) {
            if (!$referral->referral_code) {
                $referral->referral_code = static::generateReferralCode();
            }
        });
    }

    /**
     * Generate a unique referral code
     */
    protected static function generateReferralCode(): string
    {
        do {
            $code = 'REF-' . strtoupper(Str::random(8));
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Get the user who referred
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Get the user who was referred
     */
    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    /**
     * Mark referral as completed and distribute rewards
     */
    public function complete(): void
    {
        if ($this->status === 'completed') {
            return;
        }

        $this->status = 'completed';
        $this->completed_at = now();

        // Award points to referrer
        if ($this->referrer_reward > 0) {
            $this->referrer->addLoyaltyPoints($this->referrer_reward);
        }

        // Award points to referred user
        if ($this->referred_reward > 0) {
            $this->referred->addLoyaltyPoints($this->referred_reward);
        }

        $this->save();
    }

    /**
     * Scope for completed referrals
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending referrals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for referrer's referrals
     */
    public function scopeForReferrer($query, $userId)
    {
        return $query->where('referrer_id', $userId);
    }
}
