<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'ticket_id',
        'match_id',
        'quantity',
        'unit_price',
        'total_price',
        'status',
        'payment_method',
        'payment_status',
        'payment_transaction_id',
        'qr_code',
        'used_at',
        'paid_at',
        'attendee_info',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'used_at' => 'datetime',
        'paid_at' => 'datetime',
        'attendee_info' => 'array',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(Match::class);
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeUnused($query)
    {
        return $query->where('status', 'confirmed')
            ->whereNull('used_at');
    }

    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    // Methods
    public static function generateTicketNumber(): string
    {
        do {
            $ticketNumber = 'TKT-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (self::where('ticket_number', $ticketNumber)->exists());

        return $ticketNumber;
    }

    public static function generateQRCode(): string
    {
        do {
            $qrCode = 'QR-' . strtoupper(substr(md5(uniqid()), 0, 16));
        } while (self::where('qr_code', $qrCode)->exists());

        return $qrCode;
    }

    public function markAsPaid(string $transactionId): void
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_transaction_id' => $transactionId,
            'paid_at' => now(),
            'status' => 'confirmed',
            'qr_code' => self::generateQRCode(),
        ]);
    }

    public function markAsUsed(): void
    {
        $this->update([
            'status' => 'used',
            'used_at' => now(),
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
        $this->ticket->releaseTickets($this->quantity);
    }

    public function refund(): void
    {
        $this->update([
            'status' => 'refunded',
            'payment_status' => 'refunded',
        ]);
        $this->ticket->releaseTickets($this->quantity);
    }
}
