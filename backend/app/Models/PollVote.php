<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'user_id',
        'option_index',
        'voted_at',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vote) {
            if (!$vote->voted_at) {
                $vote->voted_at = now();
            }
        });
    }

    /**
     * Get the poll this vote belongs to
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Get the user who voted
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the selected option text
     */
    public function getSelectedOptionAttribute(): ?string
    {
        if (!$this->poll || !isset($this->poll->options[$this->option_index])) {
            return null;
        }

        return $this->poll->options[$this->option_index];
    }

    /**
     * Scope for specific poll
     */
    public function scopeForPoll($query, $pollId)
    {
        return $query->where('poll_id', $pollId);
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
