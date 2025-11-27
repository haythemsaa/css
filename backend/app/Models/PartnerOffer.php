<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerOffer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'partner_id',
        'title',
        'description',
        'image',
        'type',
        'discount_value_premium',
        'discount_value_socios',
        'discount_type',
        'min_purchase_amount',
        'valid_from',
        'valid_until',
        'available_days',
        'available_time_start',
        'available_time_end',
        'max_uses',
        'current_uses',
        'max_uses_per_user',
        'is_active',
        'is_featured',
        'access_level',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'available_days' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->valid_from > now()) return false;
        if ($this->valid_until && $this->valid_until < now()) return false;
        if ($this->max_uses && $this->current_uses >= $this->max_uses) return false;

        return true;
    }
}
