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
        'birth_date',
        'nationality',
        'position',
        'jersey_number',
        'photo',
        'height',
        'weight',
        'preferred_foot',
        'contract_expires_at',
        'market_value',
        'biography',
        'statistics',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'contract_expires_at' => 'date',
        'height' => 'float',
        'weight' => 'float',
        'market_value' => 'float',
        'statistics' => 'array',
    ];
}
