<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReductionUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'reduction_code_id',
        'user_id',
        'partner_id',
        'original_amount',
        'discount_amount',
        'final_amount',
        'commission_amount',
        'validated_at',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'original_amount' => 'decimal:3',
        'discount_amount' => 'decimal:3',
        'final_amount' => 'decimal:3',
        'commission_amount' => 'decimal:3',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the reduction code used
     */
    public function reductionCode()
    {
        return $this->belongsTo(ReductionCode::class);
    }

    /**
     * Get the user who used the code
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the partner where the code was used
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get savings percentage
     */
    public function getSavingsPercentageAttribute(): float
    {
        if ($this->original_amount == 0) {
            return 0;
        }

        return round(($this->discount_amount / $this->original_amount) * 100, 2);
    }

    /**
     * Scope for user's usages
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for partner's usages
     */
    public function scopeForPartner($query, $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('validated_at', [$startDate, $endDate]);
    }
}
