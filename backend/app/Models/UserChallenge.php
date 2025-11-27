<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserChallenge extends Model
{
    use HasFactory;

    protected $table = 'user_challenges';

    protected $fillable = [
        'user_id',
        'challenge_id',
        'current_progress',
        'target_value',
        'progress_percentage',
        'started_at',
        'completed_at',
        'is_completed',
        'reward_claimed',
        'progress_data',
    ];

    protected $casts = [
        'current_progress' => 'integer',
        'target_value' => 'integer',
        'progress_percentage' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_completed' => 'boolean',
        'reward_claimed' => 'boolean',
        'progress_data' => 'array',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    // Methods
    public function updateProgress(int $progress): void
    {
        $this->current_progress = $progress;
        $this->progress_percentage = min(100, ($progress / $this->target_value) * 100);

        if ($this->current_progress >= $this->target_value && !$this->is_completed) {
            $this->markAsCompleted();
        }

        $this->save();
    }

    public function markAsCompleted(): void
    {
        $this->is_completed = true;
        $this->completed_at = now();
        $this->progress_percentage = 100;
        $this->save();

        // Increment challenge completion count
        $this->challenge->increment('completion_count');
    }

    public function claimReward(): bool
    {
        if (!$this->is_completed || $this->reward_claimed) {
            return false;
        }

        $this->reward_claimed = true;
        $this->save();

        // Award points to user
        $this->user->increment('points', $this->challenge->points_reward);

        return true;
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeInProgress($query)
    {
        return $query->where('is_completed', false)
            ->where('current_progress', '>', 0);
    }

    public function scopeNotStarted($query)
    {
        return $query->where('current_progress', 0)
            ->where('is_completed', false);
    }
}
