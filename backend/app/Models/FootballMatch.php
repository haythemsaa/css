<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FootballMatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'matches';

    protected $fillable = [
        'match_date',
        'kick_off_time',
        'competition',
        'season',
        'opponent_team',
        'opponent_logo',
        'venue',
        'is_home',
        'css_score',
        'opponent_score',
        'match_status',
        'attendance',
        'match_report',
        'highlights_url',
    ];

    protected $casts = [
        'match_date' => 'date',
        'kick_off_time' => 'datetime',
        'css_score' => 'integer',
        'opponent_score' => 'integer',
        'attendance' => 'integer',
    ];

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('match_status', 'scheduled')
            ->where('match_date', '>=', now());
    }

    public function scopeLive($query)
    {
        return $query->where('match_status', 'live');
    }

    public function scopeFinished($query)
    {
        return $query->where('match_status', 'finished');
    }
}
