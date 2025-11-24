<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Match extends Model
{
    use HasFactory;

    protected $fillable = [
        'home_team_id',
        'away_team_id',
        'competition',
        'season',
        'round',
        'venue',
        'match_date',
        'home_score',
        'away_score',
        'home_halftime_score',
        'away_halftime_score',
        'status',
        'home_possession',
        'away_possession',
        'home_shots',
        'away_shots',
        'home_shots_on_target',
        'away_shots_on_target',
        'home_corners',
        'away_corners',
        'home_fouls',
        'away_fouls',
        'home_yellow_cards',
        'away_yellow_cards',
        'home_red_cards',
        'away_red_cards',
        'referee',
        'attendance',
        'match_report',
        'video_highlights_url',
        'home_lineup',
        'away_lineup',
        'home_substitutes',
        'away_substitutes',
        'match_events',
        'tv_channel',
        'live_stream_url',
        'has_live_commentary',
    ];

    protected $casts = [
        'match_date' => 'datetime',
        'home_lineup' => 'array',
        'away_lineup' => 'array',
        'home_substitutes' => 'array',
        'away_substitutes' => 'array',
        'match_events' => 'array',
        'has_live_commentary' => 'boolean',
    ];

    // Relationships
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeLive($query)
    {
        return $query->whereIn('status', ['live', 'halftime']);
    }

    public function scopeFinished($query)
    {
        return $query->where('status', 'finished');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
            ->where('match_date', '>', now())
            ->orderBy('match_date');
    }
}
