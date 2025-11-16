<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CollectibleCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'card_type',
        'rarity',
        'image_url',
        'season',
        'total_supply',
        'release_date',
    ];

    protected $casts = [
        'total_supply' => 'integer',
        'release_date' => 'date',
    ];

    // Relations
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_cards', 'card_id', 'user_id')
            ->using(UserCard::class)
            ->withTimestamps();
    }
}
