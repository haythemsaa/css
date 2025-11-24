<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SociosBenefit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'benefit_type',
        'value',
        'points_cost',
        'stock',
        'stock_used',
        'starts_at',
        'expires_at',
        'is_active',
        'terms',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get all redemptions of this benefit
     */
    public function redemptions()
    {
        return $this->hasMany(SociosBenefitRedemption::class, 'benefit_id');
    }

    /**
     * Check if benefit is currently available
     */
    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($this->starts_at && $now->isBefore($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && $now->isAfter($this->expires_at)) {
            return false;
        }

        if ($this->stock && $this->stock_used >= $this->stock) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can redeem this benefit
     */
    public function canBeRedeemedBy(User $user): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        if ($user->user_type !== 'socios') {
            return false;
        }

        if ($this->points_cost > $user->loyalty_points) {
            return false;
        }

        return true;
    }

    /**
     * Get remaining stock
     */
    public function getRemainingStockAttribute(): ?int
    {
        if (!$this->stock) {
            return null;
        }

        return max(0, $this->stock - $this->stock_used);
    }

    /**
     * Redeem benefit for user
     */
    public function redeemFor(User $user): ?SociosBenefitRedemption
    {
        if (!$this->canBeRedeemedBy($user)) {
            return null;
        }

        // Deduct points from user
        $user->loyalty_points -= $this->points_cost;
        $user->save();

        // Increment stock used
        $this->stock_used += 1;
        $this->save();

        // Create redemption record
        return SociosBenefitRedemption::create([
            'benefit_id' => $this->id,
            'user_id' => $user->id,
            'points_spent' => $this->points_cost,
            'redeemed_at' => now(),
            'status' => 'pending',
        ]);
    }

    /**
     * Scope for active benefits
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('stock')
                  ->orWhereColumn('stock_used', '<', 'stock');
            });
    }
}
