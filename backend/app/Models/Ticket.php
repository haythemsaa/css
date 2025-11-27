<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'category',
        'section',
        'price',
        'total_quantity',
        'available_quantity',
        'sold_quantity',
        'is_available',
        'sale_starts_at',
        'sale_ends_at',
        'benefits',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total_quantity' => 'integer',
        'available_quantity' => 'integer',
        'sold_quantity' => 'integer',
        'is_available' => 'boolean',
        'sale_starts_at' => 'datetime',
        'sale_ends_at' => 'datetime',
        'benefits' => 'array',
    ];

    // Relations
    public function match(): BelongsTo
    {
        return $this->belongsTo(Match::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(TicketPurchase::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->where('available_quantity', '>', 0)
            ->where(function ($q) {
                $q->whereNull('sale_starts_at')
                    ->orWhere('sale_starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('sale_ends_at')
                    ->orWhere('sale_ends_at', '>=', now());
            });
    }

    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeForMatch($query, int $matchId)
    {
        return $query->where('match_id', $matchId);
    }

    // Accessors
    public function getIsSaleActiveAttribute(): bool
    {
        if (!$this->is_available || $this->available_quantity <= 0) {
            return false;
        }

        if ($this->sale_starts_at && $this->sale_starts_at->isFuture()) {
            return false;
        }

        if ($this->sale_ends_at && $this->sale_ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function getOccupancyRateAttribute(): float
    {
        if ($this->total_quantity === 0) {
            return 0;
        }

        return round(($this->sold_quantity / $this->total_quantity) * 100, 2);
    }

    // Methods
    public function reserveTickets(int $quantity): bool
    {
        if ($this->available_quantity < $quantity) {
            return false;
        }

        $this->decrement('available_quantity', $quantity);
        $this->increment('sold_quantity', $quantity);

        return true;
    }

    public function releaseTickets(int $quantity): void
    {
        $this->increment('available_quantity', $quantity);
        $this->decrement('sold_quantity', $quantity);
    }
}
