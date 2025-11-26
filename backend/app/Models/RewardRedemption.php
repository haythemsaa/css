<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reward_id',
        'redemption_code',
        'tokens_spent',
        'transaction_id',
        'status',
        'fulfilled_at',
        'expires_at',
        'fulfillment_notes',
        'fulfilled_by',
        'delivery_method',
        'delivery_address',
        'tracking_number',
    ];

    protected $casts = [
        'tokens_spent' => 'integer',
        'fulfilled_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(FanTokenTransaction::class);
    }

    public function fulfiller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fulfilled_by');
    }

    // Accessors
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'processing' => 'En cours',
            'fulfilled' => 'Livré',
            'cancelled' => 'Annulé',
            'expired' => 'Expiré',
            default => $this->status,
        };
    }

    public function getIsActiveAttribute(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'processing']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'fulfilled');
    }

    // Methods
    public function fulfill(User $fulfiller, string $notes = null): void
    {
        $this->update([
            'status' => 'fulfilled',
            'fulfilled_at' => now(),
            'fulfilled_by' => $fulfiller->id,
            'fulfillment_notes' => $notes,
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
        
        // Refund tokens
        if ($this->user->fanTokenWallet) {
            $this->user->fanTokenWallet->addTokens(
                $this->tokens_spent,
                'refund',
                "Remboursement: {$this->reward->name}"
            );
        }

        // Increment reward stock
        $this->reward->incrementStock();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($redemption) {
            $redemption->redemption_code = strtoupper(substr(md5(uniqid()), 0, 8));
            
            // Set expiration (30 days by default)
            if (!$redemption->expires_at) {
                $redemption->expires_at = now()->addDays(30);
            }
        });
    }
}
