<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'buyer_id',
        'seller_id',
        'transaction_number',
        'ticket_price',
        'platform_fee',
        'total_amount',
        'payment_status',
        'payment_method',
        'payment_reference',
        'paid_at',
        'transfer_status',
        'transferred_at',
        'transfer_notes',
        'delivery_method',
        'meeting_details',
        'meeting_scheduled_at',
        'buyer_reviewed',
        'seller_reviewed',
    ];

    protected $casts = [
        'ticket_price' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'buyer_reviewed' => 'boolean',
        'seller_reviewed' => 'boolean',
        'paid_at' => 'datetime',
        'transferred_at' => 'datetime',
        'meeting_scheduled_at' => 'datetime',
    ];

    // Relationships
    public function listing(): BelongsTo
    {
        return $this->belongsTo(TicketListing::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(MarketplaceReview::class, 'transaction_id');
    }

    // Accessors
    public function getIsCompletedAttribute(): bool
    {
        return $this->payment_status === 'completed' && 
               $this->transfer_status === 'transferred';
    }

    public function getCanReviewAttribute(): bool
    {
        return $this->is_completed && 
               (!$this->buyer_reviewed || !$this->seller_reviewed);
    }

    public function getDeliveryMethodDisplayAttribute(): string
    {
        return match($this->delivery_method) {
            'electronic' => 'Électronique',
            'meeting' => 'Rencontre en personne',
            'courier' => 'Courrier',
            default => $this->delivery_method,
        };
    }

    // Methods
    public function markAsPaid(string $method, string $reference): void
    {
        $this->update([
            'payment_status' => 'completed',
            'payment_method' => $method,
            'payment_reference' => $reference,
            'paid_at' => now(),
        ]);
    }

    public function markAsTransferred(string $notes = null): void
    {
        $this->update([
            'transfer_status' => 'transferred',
            'transferred_at' => now(),
            'transfer_notes' => $notes,
        ]);
    }

    public function refund(): void
    {
        $this->update([
            'payment_status' => 'refunded',
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            $transaction->transaction_number = 'TKT-' . strtoupper(uniqid());
        });
    }
}
