<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'match_id',
        'predicted_home_score',
        'predicted_away_score',
        'predicted_result',
        'predicted_first_scorer_id',
        'bonus_predictions',
        'points_earned',
        'is_processed',
        'predicted_at',
    ];

    protected $casts = [
        'predicted_home_score' => 'integer',
        'predicted_away_score' => 'integer',
        'bonus_predictions' => 'array',
        'points_earned' => 'integer',
        'is_processed' => 'boolean',
        'predicted_at' => 'datetime',
    ];

    protected $appends = [
        'predicted_score_display',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(Match::class);
    }

    public function predictedFirstScorer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'predicted_first_scorer_id');
    }

    // Scopes
    public function scopeProcessed($query)
    {
        return $query->where('is_processed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_processed', false);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getPredictedScoreDisplayAttribute(): string
    {
        if ($this->predicted_home_score === null || $this->predicted_away_score === null) {
            return 'N/A';
        }

        return "{$this->predicted_home_score} - {$this->predicted_away_score}";
    }

    // Methods
    public function calculatePoints(): int
    {
        $match = $this->match;

        // Match must be completed
        if ($match->status !== 'finished' || $match->home_score === null || $match->away_score === null) {
            return 0;
        }

        $points = 0;
        $actualResult = $this->getActualResult($match);

        // Exact score: 5 points
        if ($this->predicted_home_score === $match->home_score &&
            $this->predicted_away_score === $match->away_score) {
            $points += 5;
        }
        // Correct result (win/draw/loss): 3 points
        elseif ($this->predicted_result === $actualResult) {
            $points += 3;
        }
        // Correct goal difference: 2 points
        elseif ($this->getGoalDifference($this->predicted_home_score, $this->predicted_away_score) ===
                $this->getGoalDifference($match->home_score, $match->away_score)) {
            $points += 2;
        }
        // At least one correct score: 1 point
        elseif ($this->predicted_home_score === $match->home_score ||
                $this->predicted_away_score === $match->away_score) {
            $points += 1;
        }

        // Bonus: First scorer prediction (2 points)
        if ($this->predicted_first_scorer_id && $this->checkFirstScorerCorrect($match)) {
            $points += 2;
        }

        return $points;
    }

    public function process(): void
    {
        if ($this->is_processed) {
            return;
        }

        $points = $this->calculatePoints();

        $this->update([
            'points_earned' => $points,
            'is_processed' => true,
        ]);

        // Update user's leaderboard
        $this->user->updatePredictionLeaderboard($this);
    }

    private function getActualResult(Match $match): string
    {
        if ($match->home_score > $match->away_score) {
            return 'home_win';
        } elseif ($match->home_score < $match->away_score) {
            return 'away_win';
        }
        return 'draw';
    }

    private function getGoalDifference(int $homeScore, int $awayScore): int
    {
        return abs($homeScore - $awayScore);
    }

    private function checkFirstScorerCorrect(Match $match): bool
    {
        // This would need to check match events/statistics
        // For now, return false - implement with actual match data
        return false;
    }

    public function canEdit(): bool
    {
        $match = $this->match;

        // Can't edit if match has started
        if ($match->match_date && now()->greaterThanOrEqualTo($match->match_date)) {
            return false;
        }

        // Can't edit if already processed
        return !$this->is_processed;
    }
}
