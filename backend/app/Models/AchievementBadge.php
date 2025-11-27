<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AchievementBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'category',
        'rarity',
        'criteria',
        'points_reward',
        'is_active',
        'order',
    ];

    protected $casts = [
        'criteria' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get all users who have earned this badge
     */
    public function userBadges()
    {
        return $this->hasMany(UserBadge::class, 'badge_id');
    }

    /**
     * Get users who earned this badge
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges', 'badge_id', 'user_id')
            ->withPivot('earned_at', 'progress')
            ->withTimestamps();
    }

    /**
     * Check if user has earned this badge
     */
    public function hasBeenEarnedBy(User $user): bool
    {
        return $this->userBadges()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Calculate user's progress towards this badge (0-100)
     */
    public function calculateProgress(User $user): int
    {
        if ($this->hasBeenEarnedBy($user)) {
            return 100;
        }

        if (!$this->criteria) {
            return 0;
        }

        $type = $this->criteria['type'] ?? null;
        $target = $this->criteria['target'] ?? 1;

        $current = match($type) {
            'loyalty_points' => $user->loyalty_points,
            'donations_count' => $user->donations()->count(),
            'donations_amount' => $user->donations()->sum('amount'),
            'reductions_used' => $user->reductionUsages()->count(),
            'forum_posts' => $user->forumReplies()->count(),
            'cards_collected' => $user->cards()->count(),
            'referrals' => $user->referredUsers()->count(),
            'days_active' => $user->created_at->diffInDays(now()),
            default => 0,
        };

        return min(100, (int) (($current / $target) * 100));
    }

    /**
     * Check if user meets criteria for this badge
     */
    public function checkCriteria(User $user): bool
    {
        if (!$this->criteria || $this->hasBeenEarnedBy($user)) {
            return false;
        }

        $progress = $this->calculateProgress($user);
        return $progress >= 100;
    }

    /**
     * Award this badge to a user
     */
    public function awardTo(User $user): ?UserBadge
    {
        if ($this->hasBeenEarnedBy($user)) {
            return null;
        }

        $userBadge = UserBadge::create([
            'user_id' => $user->id,
            'badge_id' => $this->id,
            'earned_at' => now(),
            'progress' => 100,
        ]);

        // Award points to user
        if ($this->points_reward > 0) {
            $user->addLoyaltyPoints($this->points_reward);
        }

        return $userBadge;
    }

    /**
     * Scope for active badges
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope by rarity
     */
    public function scopeByRarity($query, $rarity)
    {
        return $query->where('rarity', $rarity);
    }
}
