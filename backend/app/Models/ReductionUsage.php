<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReductionUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'partner_id',
        'offer_id',
        'code_id',
        'used_at',
        'location_lat',
        'location_lng',
        'original_amount',
        'discount_amount',
        'final_amount',
        'commission_earned',
        'commission_paid_at',
        'validated_by_user_id',
        'user_satisfaction_rating',
        'user_feedback',
        'status',
        'dispute_reason',
        'dispute_resolved_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'commission_paid_at' => 'datetime',
        'dispute_resolved_at' => 'datetime',
        'location_lat' => 'float',
        'location_lng' => 'float',
        'original_amount' => 'float',
        'discount_amount' => 'float',
        'final_amount' => 'float',
        'commission_earned' => 'float',
        'user_satisfaction_rating' => 'integer',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(PartnerOffer::class, 'offer_id');
    }

    public function code(): BelongsTo
    {
        return $this->belongsTo(ReductionCode::class, 'code_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by_user_id');
    }
}
