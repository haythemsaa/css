<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionWinner extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_product_id',
        'user_id',
        'winning_bid_id',
        'final_price',
        'payment_status',
        'payment_method_id',
        'transaction_id',
        'paid_at',
        'delivery_status',
        'tracking_number',
        'delivered_at',
        'notes',
    ];

    protected $casts = [
        'final_price' => 'decimal:2',
        'paid_at' => 'datetime',
        'delivered_at' => 'datetime',
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

    public function winningBid(): BelongsTo
    {
        return $this->belongsTo(AuctionBid::class, 'winning_bid_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
