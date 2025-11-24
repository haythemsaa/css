<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'partner_id',
    ];

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the partner
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Scope for user's favorites
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for partner's favorites
     */
    public function scopeForPartner($query, $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }
}
