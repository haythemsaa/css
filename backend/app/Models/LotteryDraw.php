<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryDraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'ticket_price',
        'max_tickets',
        'tickets_sold',
        'prize_description',
        'prize_value',
        'draw_date',
        'status',
        'winner_id',
        'drawn_at',
        'terms',
    ];

    protected $casts = [
        'draw_date' => 'datetime',
        'drawn_at' => 'datetime',
        'ticket_price' => 'decimal:3',
        'prize_value' => 'decimal:3',
    ];

    /**
     * Get the tickets for this draw
     */
    public function tickets()
    {
        return $this->hasMany(LotteryTicket::class, 'draw_id');
    }

    /**
     * Get the winner
     */
    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    /**
     * Check if draw is active and accepting tickets
     */
    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->draw_date->isFuture()
            && $this->tickets_sold < $this->max_tickets;
    }

    /**
     * Check if draw is ready to be drawn
     */
    public function isReadyToDraw(): bool
    {
        return $this->status === 'active'
            && $this->draw_date->isPast()
            && !$this->drawn_at;
    }

    /**
     * Get remaining tickets
     */
    public function getRemainingTicketsAttribute(): int
    {
        return $this->max_tickets - $this->tickets_sold;
    }

    /**
     * Perform the draw and select a winner
     */
    public function performDraw(): ?LotteryTicket
    {
        if (!$this->isReadyToDraw()) {
            return null;
        }

        // Get all tickets for this draw
        $tickets = $this->tickets()->get();

        if ($tickets->isEmpty()) {
            return null;
        }

        // Randomly select a winning ticket
        $winningTicket = $tickets->random();

        $this->winner_id = $winningTicket->user_id;
        $this->drawn_at = now();
        $this->status = 'completed';
        $this->save();

        // Mark winning ticket
        $winningTicket->is_winner = true;
        $winningTicket->save();

        return $winningTicket;
    }

    /**
     * Scope for active lotteries
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('draw_date', '>', now())
            ->whereColumn('tickets_sold', '<', 'max_tickets');
    }

    /**
     * Scope for upcoming draws
     */
    public function scopeUpcoming($query)
    {
        return $query->where('draw_date', '>', now())
            ->orderBy('draw_date', 'asc');
    }
}
