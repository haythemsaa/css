<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_id',
        'acquired_at',
        'acquisition_method',
        'is_favorite',
        'is_locked',
        'condition',
    ];

    protected $casts = [
        'acquired_at' => 'datetime',
        'is_favorite' => 'boolean',
        'is_locked' => 'boolean',
    ];

    /**
     * Get the user who owns this card
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the collectible card
     */
    public function card()
    {
        return $this->belongsTo(CollectibleCard::class);
    }

    /**
     * Get trades where this card is involved
     */
    public function trades()
    {
        return $this->hasMany(CardTrade::class, 'offered_user_card_id')
            ->orWhere('requested_user_card_id', $this->id);
    }

    /**
     * Check if card is available for trading
     */
    public function isAvailableForTrade(): bool
    {
        if ($this->is_locked || !$this->card->is_tradeable) {
            return false;
        }

        // Check if card is not in any pending trades
        $hasPendingTrade = CardTrade::where('status', 'pending')
            ->where(function ($query) {
                $query->where('offered_user_card_id', $this->id)
                      ->orWhere('requested_user_card_id', $this->id);
            })
            ->exists();

        return !$hasPendingTrade;
    }

    /**
     * Lock the card to prevent trading
     */
    public function lock(): void
    {
        $this->is_locked = true;
        $this->save();
    }

    /**
     * Unlock the card
     */
    public function unlock(): void
    {
        $this->is_locked = false;
        $this->save();
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(): void
    {
        $this->is_favorite = !$this->is_favorite;
        $this->save();
    }

    /**
     * Scope for user's cards
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for favorites
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Scope for tradeable cards
     */
    public function scopeTradeable($query)
    {
        return $query->where('is_locked', false)
            ->whereHas('card', function ($q) {
                $q->where('is_tradeable', true);
            });
    }
}
