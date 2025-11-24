<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'campaign_id',
        'amount',
        'currency',
        'payment_method',
        'transaction_id',
        'payment_gateway',
        'status',
        'is_anonymous',
        'donor_name',
        'donor_email',
        'is_recurring',
        'recurring_interval',
        'next_donation_at',
        'certificate_number',
        'certificate_pdf',
        'message',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_recurring' => 'boolean',
        'next_donation_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }
}
