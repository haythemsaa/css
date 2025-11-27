<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FantasyTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'league_id',
        'name',
        'budget_remaining',
        'total_points',
        'gameweek_points',
        'transfers_made',
        'transfers_available',
        'is_active',
    ];

    protected $casts = [
        'budget_remaining' => 'integer',
        'total_points' => 'integer',
        'gameweek_points' => 'integer',
        'transfers_made' => 'integer',
        'transfers_available' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'total_players',
        'squad_value',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(FantasyLeague::class, 'league_id');
    }

    public function players(): HasMany
    {
        return $this->hasMany(FantasyTeamPlayer::class);
    }

    public function starters(): HasMany
    {
        return $this->hasMany(FantasyTeamPlayer::class)->where('is_starter', true);
    }

    public function substitutes(): HasMany
    {
        return $this->hasMany(FantasyTeamPlayer::class)->where('is_starter', false);
    }

    public function gameweekScores(): HasMany
    {
        return $this->hasMany(FantasyGameweekScore::class);
    }

    // Accessors
    public function getTotalPlayersAttribute(): int
    {
        return $this->players()->count();
    }

    public function getSquadValueAttribute(): int
    {
        return $this->players()->sum('current_value');
    }

    // Methods
    public function addPlayer(Player $player, string $position, bool $isStarter = true): bool
    {
        // Check squad size (15 max)
        if ($this->total_players >= 15) {
            return false;
        }

        // Check position limits
        if (!$this->canAddPosition($position)) {
            return false;
        }

        // Check budget
        if ($player->fantasy_value > $this->budget_remaining) {
            return false;
        }

        // Add player
        $this->players()->create([
            'player_id' => $player->id,
            'position' => $position,
            'is_starter' => $isStarter,
            'purchase_price' => $player->fantasy_value,
            'current_value' => $player->fantasy_value,
            'added_at' => now(),
        ]);

        // Update budget
        $this->budget_remaining -= $player->fantasy_value;
        $this->save();

        return true;
    }

    public function removePlayer(FantasyTeamPlayer $teamPlayer): bool
    {
        $value = $teamPlayer->current_value;

        $teamPlayer->delete();

        // Refund budget
        $this->budget_remaining += $value;
        $this->transfers_made++;
        $this->save();

        return true;
    }

    public function setCaptain(FantasyTeamPlayer $teamPlayer): void
    {
        // Remove existing captain
        $this->players()->update(['is_captain' => false, 'is_vice_captain' => false]);

        // Set new captain
        $teamPlayer->update(['is_captain' => true]);
    }

    private function canAddPosition(string $position): bool
    {
        $counts = [
            'GK' => $this->players()->where('position', 'GK')->count(),
            'DEF' => $this->players()->where('position', 'DEF')->count(),
            'MID' => $this->players()->where('position', 'MID')->count(),
            'FWD' => $this->players()->where('position', 'FWD')->count(),
        ];

        $limits = [
            'GK' => 2,   // Max 2 goalkeepers
            'DEF' => 5,  // Max 5 defenders
            'MID' => 5,  // Max 5 midfielders
            'FWD' => 3,  // Max 3 forwards
        ];

        return $counts[$position] < $limits[$position];
    }

    public function calculateGameweekPoints(FantasyGameweek $gameweek): int
    {
        $points = 0;

        foreach ($this->starters as $teamPlayer) {
            $playerStats = PlayerFantasyStats::where('player_id', $teamPlayer->player_id)
                ->where('gameweek_id', $gameweek->id)
                ->first();

            if ($playerStats) {
                $playerPoints = $playerStats->points;

                // Double points for captain
                if ($teamPlayer->is_captain) {
                    $playerPoints *= 2;
                }

                $points += $playerPoints;
                $teamPlayer->points_earned += $playerPoints;
                $teamPlayer->save();
            }
        }

        return $points;
    }
}
