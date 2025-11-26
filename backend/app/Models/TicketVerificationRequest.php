<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketVerificationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'requested_by',
        'status',
        'verification_documents',
        'notes',
        'reviewed_by',
        'review_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'verification_documents' => 'array',
        'reviewed_at' => 'datetime',
    ];

    // Relationships
    public function listing(): BelongsTo
    {
        return $this->belongsTo(TicketListing::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInReview($query)
    {
        return $query->where('status', 'in_review');
    }

    // Methods
    public function approve(User $reviewer, string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $reviewer->id,
            'review_notes' => $notes,
            'reviewed_at' => now(),
        ]);

        $this->listing->verify($reviewer);
    }

    public function reject(User $reviewer, string $notes): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'review_notes' => $notes,
            'reviewed_at' => now(),
        ]);

        $this->listing->reject();
    }
}
