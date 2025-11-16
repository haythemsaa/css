<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotteryDraw extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'prize_description',
        'ticket_price',
        'total_tickets',
        'tickets_sold',
        'draw_date',
        'winner_user_id',
        'status',
    ];

    protected $casts = [
        'ticket_price' => 'float',
        'total_tickets' => 'integer',
        'tickets_sold' => 'integer',
        'draw_date' => 'datetime',
    ];

    // Relations
    public function tickets(): HasMany
    {
        return $this->hasMany(LotteryTicket::class, 'draw_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('draw_date', '>', now())
            ->where('status', 'active');
    }

    public function scopeDrawn($query)
    {
        return $query->where('status', 'drawn');
    }
}
