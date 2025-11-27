<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leaderboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leaderboard_type',
        'score',
        'rank',
        'previous_rank',
        'period',
        'period_start',
        'period_end',
        'metadata',
        'last_updated_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'rank' => 'integer',
        'previous_rank' => 'integer',
        'metadata' => 'array',
        'period_start' => 'date',
        'period_end' => 'date',
        'last_updated_at' => 'datetime',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeOfType($query, string $type)
    {
        return $query->where('leaderboard_type', $type);
    }

    public function scopeOfPeriod($query, string $period)
    {
        return $query->where('period', $period);
    }

    public function scopeTopRanked($query, int $limit = 100)
    {
        return $query->orderBy('rank', 'asc')->limit($limit);
    }

    // Accessors
    public function getRankChangeAttribute(): ?int
    {
        if ($this->previous_rank === null) {
            return null;
        }

        return $this->previous_rank - $this->rank;
    }

    public function getRankTrendAttribute(): ?string
    {
        $change = $this->rank_change;

        if ($change === null) {
            return null;
        }

        if ($change > 0) {
            return 'up';
        } elseif ($change < 0) {
            return 'down';
        }

        return 'stable';
    }
}
