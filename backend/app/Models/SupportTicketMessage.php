<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id', 'user_id', 'message', 'is_staff_reply',
        'attachments', 'is_internal_note',
    ];

    protected $casts = [
        'is_staff_reply' => 'boolean',
        'attachments' => 'array',
        'is_internal_note' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
