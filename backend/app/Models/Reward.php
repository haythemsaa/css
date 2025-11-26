<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reward extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'category',
        'token_cost',
        'monetary_value',
        'stock_quantity',
        'stock_remaining',
        'is_active',
        'is_featured',
        'minimum_level',
        'images',
        'terms_and_conditions',
        'valid_from',
        'valid_until',
        'max_per_user',
        'max_per_day',
        'total_redeemed',
        'popularity_score',
    ];

    protected $casts = [
        'token_cost' => 'integer',
        'monetary_value' => 'decimal:2',
        'stock_quantity' => 'integer',
        'stock_remaining' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'minimum_level' => 'integer',
        'images' => 'array',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'max_per_user' => 'integer',
        'max_per_day' => 'integer',
        'total_redeemed' => 'integer',
        'popularity_score' => 'integer',
    ];

    // Relationships
    public function redemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class);
    }

    // Accessors
    public function getIsAvailableAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->stock_remaining !== null && $this->stock_remaining <= 0) {
            return false;
        }

        if ($this->valid_from && now()->isBefore($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && now()->isAfter($this->valid_until)) {
            return false;
        }

        return true;
    }

    public function getCategoryDisplayAttribute(): string
    {
        return match($this->category) {
            'merchandise' => 'Marchandise',
            'experience' => 'Expérience',
            'discount' => 'Réduction',
            'digital' => 'Digital',
            'exclusive' => 'Exclusif',
            default => $this->category,
        };
    }

    public function getDiscountPercentageAttribute(): ?float
    {
        if ($this->monetary_value && $this->token_cost > 0) {
            $tokenValue = 0.10; // Example: 1 token = 0.10 TND
            $discount = (($this->monetary_value - ($this->token_cost * $tokenValue)) / $this->monetary_value) * 100;
            return max(0, $discount);
        }
        return null;
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('stock_remaining')
                          ->orWhere('stock_remaining', '>', 0);
                    })
                    ->where(function($q) {
                        $q->whereNull('valid_from')
                          ->orWhere('valid_from', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('valid_until')
                          ->orWhere('valid_until', '>=', now());
                    });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeAffordable($query, float $tokenBalance)
    {
        return $query->where('token_cost', '<=', $tokenBalance);
    }

    // Methods
    public function canBeRedeemedBy(User $user): bool
    {
        if (!$this->is_available) {
            return false;
        }

        // Check user level
        if ($user->fanTokenWallet && $user->fanTokenWallet->level < $this->minimum_level) {
            return false;
        }

        // Check user balance
        if ($user->fanTokenWallet && $user->fanTokenWallet->balance < $this->token_cost) {
            return false;
        }

        // Check per-user limit
        if ($this->max_per_user) {
            $userRedemptions = $this->redemptions()
                ->where('user_id', $user->id)
                ->whereIn('status', ['pending', 'processing', 'fulfilled'])
                ->count();
            
            if ($userRedemptions >= $this->max_per_user) {
                return false;
            }
        }

        // Check daily limit
        if ($this->max_per_day) {
            $todayRedemptions = $this->redemptions()
                ->whereDate('created_at', today())
                ->whereIn('status', ['pending', 'processing', 'fulfilled'])
                ->count();
            
            if ($todayRedemptions >= $this->max_per_day) {
                return false;
            }
        }

        return true;
    }

    public function decrementStock(): void
    {
        if ($this->stock_remaining !== null) {
            $this->decrement('stock_remaining');
        }
        $this->increment('total_redeemed');
        $this->increment('popularity_score', 10);
    }

    public function incrementStock(): void
    {
        if ($this->stock_remaining !== null) {
            $this->increment('stock_remaining');
        }
        $this->decrement('total_redeemed');
    }
}
