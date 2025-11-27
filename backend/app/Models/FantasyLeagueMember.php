<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FantasyLeagueMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'league_id',
        'user_id',
        'total_points',
        'rank',
        'joined_at',
    ];

    protected $casts = [
        'total_points' => 'integer',
        'rank' => 'integer',
        'joined_at' => 'datetime',
    ];

    public function league(): BelongsTo
    {
        return $this->belongsTo(FantasyLeague::class, 'league_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
