<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FantasyTeamPlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'fantasy_team_id',
        'player_id',
        'position',
        'is_captain',
        'is_vice_captain',
        'is_starter',
        'purchase_price',
        'current_value',
        'points_earned',
        'added_at',
    ];

    protected $casts = [
        'is_captain' => 'boolean',
        'is_vice_captain' => 'boolean',
        'is_starter' => 'boolean',
        'purchase_price' => 'integer',
        'current_value' => 'integer',
        'points_earned' => 'integer',
        'added_at' => 'datetime',
    ];

    public function fantasyTeam(): BelongsTo
    {
        return $this->belongsTo(FantasyTeam::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
