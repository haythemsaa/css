<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PredictionLeaderboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_predictions',
        'correct_results',
        'correct_scores',
        'total_points',
        'current_streak',
        'best_streak',
        'accuracy_percentage',
        'rank',
    ];

    protected $casts = [
        'total_predictions' => 'integer',
        'correct_results' => 'integer',
        'correct_scores' => 'integer',
        'total_points' => 'integer',
        'current_streak' => 'integer',
        'best_streak' => 'integer',
        'accuracy_percentage' => 'float',
        'rank' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Methods
    public function updateFromPrediction(MatchPrediction $prediction): void
    {
        $this->increment('total_predictions');

        if ($prediction->points_earned >= 5) {
            // Exact score
            $this->increment('correct_scores');
            $this->increment('correct_results');
            $this->incrementStreak();
        } elseif ($prediction->points_earned >= 3) {
            // Correct result
            $this->increment('correct_results');
            $this->incrementStreak();
        } else {
            // Incorrect
            $this->resetStreak();
        }

        $this->total_points += $prediction->points_earned;
        $this->accuracy_percentage = ($this->correct_results / $this->total_predictions) * 100;

        $this->save();
    }

    private function incrementStreak(): void
    {
        $this->current_streak++;

        if ($this->current_streak > $this->best_streak) {
            $this->best_streak = $this->current_streak;
        }
    }

    private function resetStreak(): void
    {
        $this->current_streak = 0;
    }
}
