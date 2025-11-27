<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FantasyGameweek extends Model
{
    use HasFactory;

    protected $fillable = [
        'gameweek_number',
        'name',
        'starts_at',
        'ends_at',
        'is_active',
        'is_completed',
    ];

    protected $casts = [
        'gameweek_number' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'is_completed' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_active', true)
                     ->where('starts_at', '<=', now())
                     ->where('ends_at', '>=', now())
                     ->first();
    }
}
