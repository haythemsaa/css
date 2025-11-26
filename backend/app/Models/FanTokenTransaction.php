<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FanTokenTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_number',
        'type',
        'amount',
        'balance_after',
        'source_type',
        'source_id',
        'description',
        'from_user_id',
        'to_user_id',
        'reward_redemption_id',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function redemption(): BelongsTo
    {
        return $this->belongsTo(RewardRedemption::class, 'reward_redemption_id');
    }

    // Accessors
    public function getTypeDisplayAttribute(): string
    {
        return match($this->type) {
            'earn' => 'Gagné',
            'spend' => 'Dépensé',
            'transfer_in' => 'Reçu',
            'transfer_out' => 'Envoyé',
            'bonus' => 'Bonus',
            'refund' => 'Remboursement',
            default => $this->type,
        };
    }

    public function getIsPositiveAttribute(): bool
    {
        return $this->amount > 0;
    }

    // Scopes
    public function scopeEarned($query)
    {
        return $query->whereIn('type', ['earn', 'bonus', 'transfer_in', 'refund']);
    }

    public function scopeSpent($query)
    {
        return $query->whereIn('type', ['spend', 'transfer_out']);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
