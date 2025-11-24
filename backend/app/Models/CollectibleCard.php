<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectibleCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'card_type',
        'rarity',
        'season',
        'player_id',
        'image_url',
        'stats',
        'release_date',
        'total_supply',
        'is_tradeable',
        'is_active',
    ];

    protected $casts = [
        'stats' => 'array',
        'release_date' => 'date',
        'is_tradeable' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the player associated with this card (if applicable)
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get all user cards (instances) of this card
     */
    public function userCards()
    {
        return $this->hasMany(UserCard::class, 'card_id');
    }

    /**
     * Get rarity multiplier for point calculations
     */
    public function getRarityMultiplier(): float
    {
        return match($this->rarity) {
            'common' => 1.0,
            'uncommon' => 1.5,
            'rare' => 2.0,
            'epic' => 3.0,
            'legendary' => 5.0,
            default => 1.0,
        };
    }

    /**
     * Get estimated market value based on rarity and supply
     */
    public function getEstimatedValueAttribute(): int
    {
        $baseValue = 100;
        $rarityBonus = $this->getRarityMultiplier() * 50;
        $scarcityBonus = $this->total_supply ? (1000 / $this->total_supply) : 0;

        return (int) ($baseValue + $rarityBonus + $scarcityBonus);
    }

    /**
     * Get circulation (how many users own this card)
     */
    public function getCirculationAttribute(): int
    {
        return $this->userCards()->distinct('user_id')->count('user_id');
    }

    /**
     * Scope for active cards
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by rarity
     */
    public function scopeByRarity($query, $rarity)
    {
        return $query->where('rarity', $rarity);
    }

    /**
     * Scope by season
     */
    public function scopeBySeason($query, $season)
    {
        return $query->where('season', $season);
    }

    /**
     * Scope for tradeable cards
     */
    public function scopeTradeable($query)
    {
        return $query->where('is_tradeable', true);
    }
}
