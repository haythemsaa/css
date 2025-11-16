<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GiftCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'campaign_type',
        'trigger_event',
        'trigger_config',
        'eligibility_criteria',
        'gift_items',
        'budget_allocated',
        'budget_used',
        'points_threshold',
        'start_date',
        'end_date',
        'is_active',
        'is_automated',
    ];

    protected $casts = [
        'trigger_config' => 'array',
        'eligibility_criteria' => 'array',
        'gift_items' => 'array',
        'budget_allocated' => 'float',
        'budget_used' => 'float',
        'points_threshold' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_automated' => 'boolean',
    ];

    // Relations
    public function distributions(): HasMany
    {
        return $this->hasMany(GiftDistribution::class, 'campaign_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopeAutomated($query)
    {
        return $query->where('is_automated', true);
    }
}
