<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'birth_date',
        'nationality',
        'position',
        'jersey_number',
        'photo',
        'height',
        'weight',
        'bio',
        'goals',
        'assists',
        'yellow_cards',
        'red_cards',
        'matches_played',
        'contract_start',
        'contract_end',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'contract_start' => 'date',
        'contract_end' => 'date',
        'height' => 'integer',
        'weight' => 'integer',
        'jersey_number' => 'integer',
        'goals' => 'integer',
        'assists' => 'integer',
        'yellow_cards' => 'integer',
        'red_cards' => 'integer',
        'matches_played' => 'integer',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPosition($query, string $position)
    {
        return $query->where('position', $position);
    }
}
