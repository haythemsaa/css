<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReductionCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'partner_id',
        'offer_id',
        'code',
        'reduction_type',
        'reduction_value',
        'generated_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
        'reduction_value' => 'float',
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

    public function usage(): HasOne
    {
        return $this->hasOne(ReductionUsage::class, 'code_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeUnused($query)
    {
        return $query->whereDoesntHave('usage');
    }
}
