<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'type',
        'goal_amount',
        'current_amount',
        'min_donation',
        'starts_at',
        'ends_at',
        'status',
        'is_featured',
        'donors_count',
        'usage_report',
        'report_published_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_featured' => 'boolean',
        'report_published_at' => 'datetime',
    ];

    // Relationships
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    // Accessors
    public function getProgressPercentageAttribute(): float
    {
        if ($this->goal_amount <= 0) return 0;
        return min(100, round(($this->current_amount / $this->goal_amount) * 100, 2));
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->goal_amount - $this->current_amount);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
