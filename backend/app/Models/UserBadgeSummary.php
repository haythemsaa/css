<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBadgeSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_badges',
        'common_badges',
        'rare_badges',
        'epic_badges',
        'legendary_badges',
        'total_tokens_from_badges',
        'total_xp_from_badges',
        'completion_percentage',
    ];

    protected $casts = [
        'total_badges' => 'integer',
        'common_badges' => 'integer',
        'rare_badges' => 'integer',
        'epic_badges' => 'integer',
        'legendary_badges' => 'integer',
        'total_tokens_from_badges' => 'integer',
        'total_xp_from_badges' => 'integer',
        'completion_percentage' => 'float',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
