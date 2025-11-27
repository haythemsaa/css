<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BadgeStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'badge_id',
        'total_unlocked',
        'total_in_progress',
        'unlock_percentage',
        'first_unlocked_at',
        'last_unlocked_at',
    ];

    protected $casts = [
        'total_unlocked' => 'integer',
        'total_in_progress' => 'integer',
        'unlock_percentage' => 'float',
        'first_unlocked_at' => 'datetime',
        'last_unlocked_at' => 'datetime',
    ];

    // Relationships
    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }
}
