<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TicketListing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'seller_id',
        'match_id',
        'ticket_number',
        'category',
        'section',
        'row',
        'seat_number',
        'quantity',
        'original_price',
        'selling_price',
        'platform_fee_percentage',
        'status',
        'is_verified',
        'verified_at',
        'verified_by',
        'description',
        'images',
        'is_featured',
        'allow_negotiation',
        'minimum_price',
        'reserved_by',
        'reserved_until',
        'listed_at',
        'sold_at',
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'platform_fee_percentage' => 'decimal:2',
        'minimum_price' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'allow_negotiation' => 'boolean',
        'images' => 'array',
        'verified_at' => 'datetime',
        'reserved_until' => 'datetime',
        'listed_at' => 'datetime',
        'sold_at' => 'datetime',
    ];

    // Relationships
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(Match::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function reservedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reserved_by');
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(TicketTransaction::class, 'listing_id');
    }

    public function verificationRequest(): HasOne
    {
        return $this->hasOne(TicketVerificationRequest::class, 'listing_id');
    }

    // Accessors
    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'available' && $this->is_verified;
    }

    public function getIsReservedAttribute(): bool
    {
        return $this->status === 'reserved' && 
               $this->reserved_until && 
               $this->reserved_until->isFuture();
    }

    public function getPlatformFeeAttribute(): float
    {
        return ($this->selling_price * $this->platform_fee_percentage) / 100;
    }

    public function getSellerAmountAttribute(): float
    {
        return $this->selling_price - $this->platform_fee;
    }

    public function getCategoryDisplayAttribute(): string
    {
        return match($this->category) {
            'vip' => 'VIP',
            'tribune' => 'Tribune',
            'populaire' => 'Populaire',
            'virage' => 'Virage',
            default => $this->category,
        };
    }

    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            'pending_verification' => 'En attente de vérification',
            'available' => 'Disponible',
            'reserved' => 'Réservé',
            'sold' => 'Vendu',
            'cancelled' => 'Annulé',
            'rejected' => 'Rejeté',
            default => $this->status,
        };
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
                    ->where('is_verified', true);
    }

    public function scopeForMatch($query, int $matchId)
    {
        return $query->where('match_id', $matchId);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    // Methods
    public function reserve(User $user, int $minutes = 15): void
    {
        $this->update([
            'status' => 'reserved',
            'reserved_by' => $user->id,
            'reserved_until' => now()->addMinutes($minutes),
        ]);
    }

    public function cancelReservation(): void
    {
        $this->update([
            'status' => 'available',
            'reserved_by' => null,
            'reserved_until' => null,
        ]);
    }

    public function markAsSold(): void
    {
        $this->update([
            'status' => 'sold',
            'sold_at' => now(),
        ]);
    }

    public function verify(User $verifier): void
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $verifier->id,
            'status' => 'available',
            'listed_at' => now(),
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'status' => 'rejected',
            'is_verified' => false,
        ]);
    }
}
