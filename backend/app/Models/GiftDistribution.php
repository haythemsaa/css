<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftDistribution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'user_id',
        'gift_type',
        'gift_description',
        'gift_value',
        'distributed_at',
        'delivery_status',
        'delivered_at',
        'tracking_info',
    ];

    protected $casts = [
        'gift_value' => 'float',
        'distributed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Relations
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(GiftCampaign::class, 'campaign_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
