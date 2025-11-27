<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'options',
        'starts_at',
        'ends_at',
        'min_user_type',
        'is_active',
        'allow_multiple',
        'show_results_before_vote',
    ];

    protected $casts = [
        'options' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'allow_multiple' => 'boolean',
        'show_results_before_vote' => 'boolean',
    ];

    /**
     * Get all votes for this poll
     */
    public function votes()
    {
        return $this->hasMany(PollVote::class);
    }

    /**
     * Check if poll is currently active
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        return $now->between($this->starts_at, $this->ends_at);
    }

    /**
     * Check if poll has ended
     */
    public function hasEnded(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    /**
     * Check if user has voted
     */
    public function hasUserVoted(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if user can vote
     */
    public function canUserVote(User $user): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        if ($this->hasUserVoted($user) && !$this->allow_multiple) {
            return false;
        }

        // Check user type eligibility
        if ($this->min_user_type) {
            $typeHierarchy = ['free' => 0, 'premium' => 1, 'socios' => 2, 'admin' => 3];
            $userLevel = $typeHierarchy[$user->user_type] ?? 0;
            $requiredLevel = $typeHierarchy[$this->min_user_type] ?? 0;

            return $userLevel >= $requiredLevel;
        }

        return true;
    }

    /**
     * Get vote counts by option
     */
    public function getResults(): array
    {
        $totalVotes = $this->votes()->count();
        $results = [];

        $voteCounts = $this->votes()
            ->select('option_index', \DB::raw('count(*) as count'))
            ->groupBy('option_index')
            ->pluck('count', 'option_index');

        foreach ($this->options as $index => $option) {
            $count = $voteCounts[$index] ?? 0;
            $percentage = $totalVotes > 0 ? round(($count / $totalVotes) * 100, 2) : 0;

            $results[] = [
                'option' => $option,
                'index' => $index,
                'votes' => $count,
                'percentage' => $percentage,
            ];
        }

        return $results;
    }

    /**
     * Get total votes count
     */
    public function getTotalVotesAttribute(): int
    {
        return $this->votes()->count();
    }

    /**
     * Scope for active polls
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    /**
     * Scope for upcoming polls
     */
    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>', now());
    }

    /**
     * Scope for ended polls
     */
    public function scopeEnded($query)
    {
        return $query->where('ends_at', '<', now());
    }
}
