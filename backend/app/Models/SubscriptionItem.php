<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'product_id',
        'price',
        'quantity',
    ];

    protected $casts = [
        'price' => 'decimal:3',
    ];

    /**
     * Get the subscription this item belongs to
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get total price
     */
    public function getTotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }
}
