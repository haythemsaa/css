<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerFantasyStats extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'gameweek_id',
        'goals',
        'assists',
        'clean_sheets',
        'yellow_cards',
        'red_cards',
        'minutes_played',
        'points',
    ];

    protected $casts = [
        'goals' => 'integer',
        'assists' => 'integer',
        'clean_sheets' => 'integer',
        'yellow_cards' => 'integer',
        'red_cards' => 'integer',
        'minutes_played' => 'integer',
        'points' => 'integer',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function gameweek(): BelongsTo
    {
        return $this->belongsTo(FantasyGameweek::class, 'gameweek_id');
    }
}
