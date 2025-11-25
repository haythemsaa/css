<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionTier extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'monthly_price', 'yearly_price',
        'features', 'benefits', 'badge_color', 'badge_icon',
        'points_multiplier', 'discount_percentage', 'priority_support',
        'is_active', 'display_order',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'features' => 'array',
        'benefits' => 'array',
        'points_multiplier' => 'integer',
        'discount_percentage' => 'integer',
        'priority_support' => 'boolean',
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];
}
