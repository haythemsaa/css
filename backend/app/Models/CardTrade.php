<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardTrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'initiator_id',
        'recipient_id',
        'offered_user_card_id',
        'requested_user_card_id',
        'status',
        'message',
        'responded_at',
        'completed_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user who initiated the trade
     */
    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    /**
     * Get the user who receives the trade offer
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Get the card being offered
     */
    public function offeredCard()
    {
        return $this->belongsTo(UserCard::class, 'offered_user_card_id');
    }

    /**
     * Get the card being requested
     */
    public function requestedCard()
    {
        return $this->belongsTo(UserCard::class, 'requested_user_card_id');
    }

    /**
     * Accept the trade
     */
    public function accept(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        // Verify cards are still available
        if (!$this->offeredCard->isAvailableForTrade() ||
            !$this->requestedCard->isAvailableForTrade()) {
            $this->status = 'cancelled';
            $this->save();
            return false;
        }

        // Swap card ownership
        $tempUserId = $this->offeredCard->user_id;
        $this->offeredCard->user_id = $this->requestedCard->user_id;
        $this->requestedCard->user_id = $tempUserId;

        $this->offeredCard->acquisition_method = 'trade';
        $this->requestedCard->acquisition_method = 'trade';

        $this->offeredCard->save();
        $this->requestedCard->save();

        // Update trade status
        $this->status = 'completed';
        $this->responded_at = now();
        $this->completed_at = now();
        $this->save();

        return true;
    }

    /**
     * Reject the trade
     */
    public function reject(): void
    {
        $this->status = 'rejected';
        $this->responded_at = now();
        $this->save();
    }

    /**
     * Cancel the trade
     */
    public function cancel(): void
    {
        $this->status = 'cancelled';
        $this->save();
    }

    /**
     * Check if trade can be cancelled by user
     */
    public function canBeCancelledBy(User $user): bool
    {
        return $this->status === 'pending' && $this->initiator_id === $user->id;
    }

    /**
     * Check if trade can be responded to by user
     */
    public function canBeRespondedBy(User $user): bool
    {
        return $this->status === 'pending' && $this->recipient_id === $user->id;
    }

    /**
     * Scope for pending trades
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for completed trades
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for user's trades (as initiator or recipient)
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('initiator_id', $userId)
            ->orWhere('recipient_id', $userId);
    }
}
