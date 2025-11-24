<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'auto_renew',
        'payment_method',
        'amount',
        'currency',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'auto_renew' => 'boolean',
        'amount' => 'decimal:3',
    ];

    /**
     * Get the user that owns the subscription
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get subscription items
     */
    public function items()
    {
        return $this->hasMany(SubscriptionItem::class);
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->ends_at
            && $this->ends_at->isFuture();
    }

    /**
     * Check if subscription is on trial
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Cancel subscription
     */
    public function cancel(): void
    {
        $this->status = 'cancelled';
        $this->auto_renew = false;
        $this->save();
    }

    /**
     * Resume subscription
     */
    public function resume(): void
    {
        if ($this->ends_at && $this->ends_at->isFuture()) {
            $this->status = 'active';
            $this->auto_renew = true;
            $this->save();
        }
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('ends_at', '>', now());
    }

    /**
     * Scope for expiring soon
     */
    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('status', 'active')
            ->whereBetween('ends_at', [now(), now()->addDays($days)]);
    }
}
