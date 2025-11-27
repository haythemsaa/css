<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'challenge_type',
        'category',
        'difficulty',
        'requirements',
        'target_value',
        'points_reward',
        'additional_rewards',
        'icon',
        'color',
        'max_completions',
        'starts_at',
        'ends_at',
        'is_active',
        'is_featured',
        'completion_count',
    ];

    protected $casts = [
        'requirements' => 'array',
        'additional_rewards' => 'array',
        'target_value' => 'integer',
        'points_reward' => 'integer',
        'max_completions' => 'integer',
        'completion_count' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // Relations
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_challenges')
            ->withPivot([
                'current_progress',
                'target_value',
                'progress_percentage',
                'started_at',
                'completed_at',
                'is_completed',
                'reward_claimed',
                'progress_data',
            ])
            ->withTimestamps();
    }

    public function userChallenges(): HasMany
    {
        return $this->hasMany(UserChallenge::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('challenge_type', $type);
    }

    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOfDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Accessors
    public function getIsAvailableAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function getTimeRemainingAttribute(): ?int
    {
        if (!$this->ends_at) {
            return null;
        }

        return max(0, now()->diffInSeconds($this->ends_at, false));
    }

    public function getDifficultyLevelAttribute(): int
    {
        return match($this->difficulty) {
            'easy' => 1,
            'medium' => 2,
            'hard' => 3,
            'expert' => 4,
            default => 1,
        };
    }
}
