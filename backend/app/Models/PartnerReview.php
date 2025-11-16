<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'partner_id',
        'reduction_usage_id',
        'rating',
        'title',
        'comment',
        'is_verified_purchase',
        'helpful_count',
        'reported_count',
        'moderation_status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified_purchase' => 'boolean',
        'helpful_count' => 'integer',
        'reported_count' => 'integer',
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

    public function reductionUsage(): BelongsTo
    {
        return $this->belongsTo(ReductionUsage::class, 'reduction_usage_id');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('moderation_status', 'approved');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified_purchase', true);
    }
}
