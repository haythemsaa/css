<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCard extends Model
{
    use HasFactory;

    protected $table = 'user_cards';

    protected $fillable = [
        'user_id',
        'card_id',
        'acquired_at',
        'acquisition_method',
    ];

    protected $casts = [
        'acquired_at' => 'datetime',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(CollectibleCard::class, 'card_id');
    }
}
