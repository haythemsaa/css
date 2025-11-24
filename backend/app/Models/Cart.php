<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // Methods
    public function addItem(int $productId, int $quantity = 1): CartItem
    {
        $item = $this->items()->where('product_id', $productId)->first();

        if ($item) {
            $item->increment('quantity', $quantity);
            return $item;
        }

        return $this->items()->create([
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }

    public function removeItem(int $productId): bool
    {
        return $this->items()->where('product_id', $productId)->delete() > 0;
    }

    public function updateItemQuantity(int $productId, int $quantity): bool
    {
        $item = $this->items()->where('product_id', $productId)->first();

        if (!$item) {
            return false;
        }

        if ($quantity <= 0) {
            return $item->delete();
        }

        $item->update(['quantity' => $quantity]);
        return true;
    }

    public function clear(): void
    {
        $this->items()->delete();
    }

    public function getTotal(): float
    {
        return $this->items->sum(function ($item) {
            return $item->product->current_price * $item->quantity;
        });
    }

    public function getItemsCount(): int
    {
        return $this->items->sum('quantity');
    }
}
