<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'slug',
        'jersey_number',
        'position',
        'photo',
        'date_of_birth',
        'nationality',
        'height',
        'weight',
        'preferred_foot',
        'market_value',
        'biography',
        'goals',
        'assists',
        'matches_played',
        'yellow_cards',
        'red_cards',
        'status',
        'injury_until',
        'injury_description',
        'suspension_until',
        'instagram',
        'twitter',
        'facebook',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'injury_until' => 'date',
        'suspension_until' => 'date',
    ];

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByPosition($query, string $position)
    {
        return $query->where('position', $position);
    }
}
