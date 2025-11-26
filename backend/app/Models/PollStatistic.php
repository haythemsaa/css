<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PollStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'total_votes',
        'total_voters',
        'option_results',
        'average_rating',
        'last_calculated_at',
    ];

    protected $casts = [
        'option_results' => 'array',
        'average_rating' => 'decimal:2',
        'last_calculated_at' => 'datetime',
    ];

    /**
     * Relations
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }
}
