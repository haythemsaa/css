<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number', 'user_id', 'subject', 'description', 'category',
        'priority', 'status', 'assigned_to', 'attachments',
        'first_response_at', 'resolved_at', 'satisfaction_rating',
        'satisfaction_comment',
    ];

    protected $casts = [
        'attachments' => 'array',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'satisfaction_rating' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id');
    }
}
