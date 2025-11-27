<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LotteryTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'draw_id',
        'user_id',
        'ticket_number',
        'purchased_at',
        'is_winner',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'is_winner' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (!$ticket->ticket_number) {
                $ticket->ticket_number = static::generateTicketNumber();
            }
            if (!$ticket->purchased_at) {
                $ticket->purchased_at = now();
            }
        });
    }

    /**
     * Generate a unique ticket number
     */
    protected static function generateTicketNumber(): string
    {
        do {
            $number = 'TKT-' . strtoupper(Str::random(8));
        } while (static::where('ticket_number', $number)->exists());

        return $number;
    }

    /**
     * Get the lottery draw this ticket belongs to
     */
    public function draw()
    {
        return $this->belongsTo(LotteryDraw::class);
    }

    /**
     * Get the user who purchased this ticket
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for winning tickets
     */
    public function scopeWinners($query)
    {
        return $query->where('is_winner', true);
    }

    /**
     * Scope for user's tickets
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific draw
     */
    public function scopeForDraw($query, $drawId)
    {
        return $query->where('draw_id', $drawId);
    }
}
