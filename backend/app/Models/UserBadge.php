<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'badge_id',
        'progress',
        'progress_max',
        'is_unlocked',
        'unlocked_at',
        'tokens_earned',
        'xp_earned',
        'metadata',
    ];

    protected $casts = [
        'progress' => 'integer',
        'progress_max' => 'integer',
        'is_unlocked' => 'boolean',
        'unlocked_at' => 'datetime',
        'tokens_earned' => 'integer',
        'xp_earned' => 'integer',
        'metadata' => 'array',
    ];

    protected $appends = [
        'progress_percentage',
        'is_completed',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    // Scopes
    public function scopeUnlocked($query)
    {
        return $query->where('is_unlocked', true);
    }

    public function scopeInProgress($query)
    {
        return $query->where('is_unlocked', false)->where('progress', '>', 0);
    }

    public function scopeLocked($query)
    {
        return $query->where('is_unlocked', false)->where('progress', 0);
    }

    // Accessors
    public function getProgressPercentageAttribute(): float
    {
        if ($this->progress_max == 0) {
            return $this->is_unlocked ? 100 : 0;
        }

        return min(100, round(($this->progress / $this->progress_max) * 100, 2));
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->is_unlocked || $this->progress >= $this->progress_max;
    }

    // Methods
    public function incrementProgress(int $amount = 1, ?array $metadata = null): bool
    {
        if ($this->is_unlocked) {
            return false;
        }

        $this->increment('progress', $amount);

        if ($metadata) {
            $this->update(['metadata' => array_merge($this->metadata ?? [], $metadata)]);
        }

        $this->refresh();

        // Check if badge should be unlocked
        if ($this->progress >= $this->progress_max) {
            return $this->unlock();
        }

        return false;
    }

    public function unlock(): bool
    {
        if ($this->is_unlocked) {
            return false;
        }

        $badge = $this->badge;

        // Award tokens and XP
        $wallet = $this->user->tokenWallet ?? $this->user->tokenWallet()->create([
            'balance' => 0,
            'lifetime_earned' => 0,
            'lifetime_spent' => 0,
        ]);

        $tokensEarned = $badge->token_reward;
        $xpEarned = $badge->xp_reward;

        if ($tokensEarned > 0) {
            $wallet->addTokens(
                $tokensEarned,
                'badge',
                "Badge débloqué: {$badge->name}",
                'Badge',
                $badge->id
            );
        }

        if ($xpEarned > 0) {
            $wallet->addExperience($xpEarned);
        }

        // Mark as unlocked
        $this->update([
            'is_unlocked' => true,
            'unlocked_at' => now(),
            'tokens_earned' => $tokensEarned,
            'xp_earned' => $xpEarned,
            'progress' => $this->progress_max, // Set progress to max
        ]);

        // Update badge statistics
        $badge->updateStatistics();

        // Update user summary
        $this->user->updateBadgeSummary();

        return true;
    }

    public function reset(): void
    {
        $this->update([
            'progress' => 0,
            'is_unlocked' => false,
            'unlocked_at' => null,
            'tokens_earned' => 0,
            'xp_earned' => 0,
        ]);
    }
}
