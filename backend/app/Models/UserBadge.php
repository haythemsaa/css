<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'badge_id',
        'earned_at',
        'progress',
        'is_displayed',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
        'is_displayed' => 'boolean',
    ];

    /**
     * Get the user who owns this badge
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the badge
     */
    public function badge()
    {
        return $this->belongsTo(AchievementBadge::class);
    }

    /**
     * Toggle display status
     */
    public function toggleDisplay(): void
    {
        $this->is_displayed = !$this->is_displayed;
        $this->save();
    }

    /**
     * Scope for user's badges
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for displayed badges
     */
    public function scopeDisplayed($query)
    {
        return $query->where('is_displayed', true);
    }

    /**
     * Scope for recently earned
     */
    public function scopeRecentlyEarned($query, $days = 7)
    {
        return $query->where('earned_at', '>=', now()->subDays($days));
    }
}
