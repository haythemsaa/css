<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'icon',
        'image_url',
        'rarity',
        'token_reward',
        'xp_reward',
        'is_secret',
        'is_active',
        'unlock_criteria',
        'unlock_type',
        'required_count',
        'display_order',
    ];

    protected $casts = [
        'unlock_criteria' => 'array',
        'is_secret' => 'boolean',
        'is_active' => 'boolean',
        'token_reward' => 'integer',
        'xp_reward' => 'integer',
        'required_count' => 'integer',
        'display_order' => 'integer',
    ];

    protected $appends = [
        'category_display',
        'rarity_display',
        'unlock_type_display',
    ];

    // Relationships
    public function userBadges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }

    public function statistics(): HasOne
    {
        return $this->hasOne(BadgeStatistic::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByRarity($query, string $rarity)
    {
        return $query->where('rarity', $rarity);
    }

    public function scopePublic($query)
    {
        return $query->where('is_secret', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    // Accessors
    public function getCategoryDisplayAttribute(): string
    {
        return match($this->category) {
            'social' => 'Social',
            'participation' => 'Participation',
            'achievement' => 'Accomplissement',
            'special' => 'Spécial',
            'loyalty' => 'Fidélité',
            'performance' => 'Performance',
            default => ucfirst($this->category),
        };
    }

    public function getRarityDisplayAttribute(): string
    {
        return match($this->rarity) {
            'common' => 'Commun',
            'rare' => 'Rare',
            'epic' => 'Épique',
            'legendary' => 'Légendaire',
            default => ucfirst($this->rarity),
        };
    }

    public function getUnlockTypeDisplayAttribute(): string
    {
        return match($this->unlock_type) {
            'manual' => 'Manuel',
            'auto' => 'Automatique',
            'event' => 'Événement',
            'milestone' => 'Jalon',
            'streak' => 'Série',
            default => ucfirst($this->unlock_type),
        };
    }

    // Methods
    public function updateStatistics(): void
    {
        $stats = $this->statistics ?? $this->statistics()->create([]);

        $totalUnlocked = $this->userBadges()->where('is_unlocked', true)->count();
        $totalInProgress = $this->userBadges()->where('is_unlocked', false)->where('progress', '>', 0)->count();
        $totalUsers = User::count();

        $unlockPercentage = $totalUsers > 0 ? ($totalUnlocked / $totalUsers) * 100 : 0;

        $firstUnlocked = $this->userBadges()->where('is_unlocked', true)->oldest('unlocked_at')->first();
        $lastUnlocked = $this->userBadges()->where('is_unlocked', true)->latest('unlocked_at')->first();

        $stats->update([
            'total_unlocked' => $totalUnlocked,
            'total_in_progress' => $totalInProgress,
            'unlock_percentage' => round($unlockPercentage, 2),
            'first_unlocked_at' => $firstUnlocked?->unlocked_at,
            'last_unlocked_at' => $lastUnlocked?->unlocked_at,
        ]);
    }

    public function getUserProgress(User $user): ?UserBadge
    {
        return $this->userBadges()->where('user_id', $user->id)->first();
    }

    public function isUnlockedBy(User $user): bool
    {
        return $this->userBadges()
            ->where('user_id', $user->id)
            ->where('is_unlocked', true)
            ->exists();
    }

    public function getRarityColor(): string
    {
        return match($this->rarity) {
            'common' => '#9CA3AF', // gray
            'rare' => '#3B82F6', // blue
            'epic' => '#A855F7', // purple
            'legendary' => '#F59E0B', // gold
            default => '#6B7280',
        };
    }
}
