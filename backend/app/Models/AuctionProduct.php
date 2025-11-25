<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuctionProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'images',
        'category',
        'starting_price',
        'reserve_price',
        'current_bid',
        'buy_now_price',
        'bid_increment',
        'total_bids',
        'current_winner_id',
        'start_time',
        'end_time',
        'is_featured',
        'auto_extend',
        'auto_extend_minutes',
        'status',
        'terms_conditions',
        'metadata',
    ];

    protected $casts = [
        'images' => 'array',
        'metadata' => 'array',
        'starting_price' => 'decimal:2',
        'reserve_price' => 'decimal:2',
        'current_bid' => 'decimal:2',
        'buy_now_price' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_featured' => 'boolean',
        'auto_extend' => 'boolean',
    ];

    // Relations
    public function bids(): HasMany
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function currentWinner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_winner_id');
    }

    public function winner(): HasOne
    {
        return $this->hasOne(AuctionWinner::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_time', '<=', now())
            ->where('end_time', '>', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
            ->where('start_time', '>', now());
    }

    public function scopeEnded($query)
    {
        return $query->where('status', 'ended')
            ->orWhere(function ($q) {
                $q->where('end_time', '<=', now());
            });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' &&
               $this->start_time <= now() &&
               $this->end_time > now();
    }

    public function getTimeRemainingAttribute(): ?int
    {
        if (!$this->is_active) {
            return null;
        }

        return $this->end_time->diffInSeconds(now());
    }

    public function getReserveMetAttribute(): bool
    {
        if (!$this->reserve_price) {
            return true;
        }

        return $this->current_bid >= $this->reserve_price;
    }

    // Methods
    public function placeBid(User $user, float $amount, bool $isAutoBid = false, ?float $maxAutoBid = null): AuctionBid
    {
        $bid = $this->bids()->create([
            'user_id' => $user->id,
            'bid_amount' => $amount,
            'is_auto_bid' => $isAutoBid,
            'max_auto_bid' => $maxAutoBid,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Update current bid and winner
        $this->update([
            'current_bid' => $amount,
            'current_winner_id' => $user->id,
            'total_bids' => $this->total_bids + 1,
        ]);

        // Mark previous bids as outbid
        $this->bids()
            ->where('id', '!=', $bid->id)
            ->where('is_winning', true)
            ->update([
                'is_winning' => false,
                'was_outbid' => true,
                'outbid_at' => now(),
            ]);

        // Mark current bid as winning
        $bid->update(['is_winning' => true]);

        // Auto-extend if needed
        if ($this->auto_extend && $this->end_time->diffInMinutes(now()) < $this->auto_extend_minutes) {
            $this->update([
                'end_time' => now()->addMinutes($this->auto_extend_minutes),
            ]);
        }

        return $bid;
    }

    public function canBid(User $user, float $amount): array
    {
        if (!$this->is_active) {
            return ['can_bid' => false, 'reason' => 'Cette enchère n\'est pas active'];
        }

        $minimumBid = $this->current_bid > 0
            ? $this->current_bid + $this->bid_increment
            : $this->starting_price;

        if ($amount < $minimumBid) {
            return ['can_bid' => false, 'reason' => "L'enchère minimum est de {$minimumBid} TND"];
        }

        if ($this->current_winner_id === $user->id) {
            return ['can_bid' => false, 'reason' => 'Vous êtes déjà le plus haut enchérisseur'];
        }

        return ['can_bid' => true];
    }

    public function endAuction(): void
    {
        $this->update(['status' => 'ended']);

        if ($this->current_winner_id && $this->reserve_met) {
            $winningBid = $this->bids()
                ->where('user_id', $this->current_winner_id)
                ->where('is_winning', true)
                ->first();

            if ($winningBid) {
                AuctionWinner::create([
                    'auction_product_id' => $this->id,
                    'user_id' => $this->current_winner_id,
                    'winning_bid_id' => $winningBid->id,
                    'final_price' => $this->current_bid,
                ]);

                $this->update(['status' => 'sold']);
            }
        }
    }
}
