<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PollOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'text',
        'description',
        'image_url',
        'display_order',
    ];

    protected $appends = [
        'vote_count',
        'vote_percentage',
    ];

    /**
     * Relations
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    /**
     * Accessors
     */
    public function getVoteCountAttribute(): int
    {
        return $this->votes()->count();
    }

    public function getVotePercentageAttribute(): float
    {
        $totalVotes = $this->poll->total_votes;

        if ($totalVotes === 0) {
            return 0;
        }

        return round(($this->vote_count / $totalVotes) * 100, 1);
    }
}
