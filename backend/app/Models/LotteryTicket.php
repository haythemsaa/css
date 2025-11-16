<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotteryTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'draw_id',
        'ticket_number',
        'purchase_date',
        'amount_paid',
        'payment_method',
        'is_winner',
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
        'amount_paid' => 'float',
        'is_winner' => 'boolean',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function draw(): BelongsTo
    {
        return $this->belongsTo(LotteryDraw::class, 'draw_id');
    }
}
