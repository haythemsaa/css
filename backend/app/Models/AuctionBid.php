<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionBid extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_product_id',
        'user_id',
        'bid_amount',
        'is_auto_bid',
        'max_auto_bid',
        'ip_address',
        'user_agent',
        'is_winning',
        'was_outbid',
        'outbid_at',
    ];

    protected $casts = [
        'bid_amount' => 'decimal:2',
        'max_auto_bid' => 'decimal:2',
        'is_auto_bid' => 'boolean',
        'is_winning' => 'boolean',
        'was_outbid' => 'boolean',
        'outbid_at' => 'datetime',
    ];

    // Relations
    public function auctionProduct(): BelongsTo
    {
        return $this->belongsTo(AuctionProduct::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
