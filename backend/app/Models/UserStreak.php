<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserStreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_streak',
        'longest_streak',
        'last_activity_date',
        'total_active_days',
    ];

    protected $casts = [
        'last_activity_date' => 'date',
        'current_streak' => 'integer',
        'longest_streak' => 'integer',
        'total_active_days' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Update streak when user is active
    public function updateStreak(): void
    {
        $today = Carbon::today();

        // If no last activity, this is the first day
        if (!$this->last_activity_date) {
            $this->current_streak = 1;
            $this->longest_streak = 1;
            $this->last_activity_date = $today;
            $this->total_active_days = 1;
            $this->save();
            return;
        }

        // If already active today, do nothing
        if ($this->last_activity_date->isToday()) {
            return;
        }

        // If active yesterday, increment streak
        if ($this->last_activity_date->isYesterday()) {
            $this->current_streak++;
            $this->total_active_days++;

            if ($this->current_streak > $this->longest_streak) {
                $this->longest_streak = $this->current_streak;
            }
        } else {
            // Streak broken, reset to 1
            $this->current_streak = 1;
            $this->total_active_days++;
        }

        $this->last_activity_date = $today;
        $this->save();
    }

    // Check if streak is at risk (user hasn't been active today)
    public function isStreakAtRisk(): bool
    {
        if (!$this->last_activity_date) {
            return false;
        }

        return !$this->last_activity_date->isToday() && $this->current_streak > 0;
    }
}
