<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'type',
        'description',
        'logo_url',
        'provider',
        'is_active',
        'is_default',
        'supported_currencies',
        'min_amount',
        'max_amount',
        'transaction_fee',
        'transaction_fee_percentage',
        'config',
        'processing_time',
        'supports_refund',
        'instructions',
        'display_order',
        'available_for',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'supports_refund' => 'boolean',
        'supported_currencies' => 'array',
        'config' => 'array',
        'available_for' => 'array',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'transaction_fee' => 'decimal:2',
        'transaction_fee_percentage' => 'decimal:2',
    ];

    // Relations
    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForContext($query, string $context)
    {
        return $query->whereJsonContains('available_for', $context);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Methods
    public function calculateFees(float $amount): array
    {
        $fixedFee = $this->transaction_fee ?? 0;
        $percentageFee = ($amount * $this->transaction_fee_percentage) / 100;
        $totalFee = $fixedFee + $percentageFee;
        $netAmount = $amount - $totalFee;

        return [
            'amount' => $amount,
            'fixed_fee' => $fixedFee,
            'percentage_fee' => $percentageFee,
            'total_fee' => round($totalFee, 2),
            'net_amount' => round($netAmount, 2),
        ];
    }

    public function isAvailableFor(string $context): bool
    {
        return in_array($context, $this->available_for ?? []);
    }

    public function canProcessAmount(float $amount): bool
    {
        if ($this->min_amount && $amount < $this->min_amount) {
            return false;
        }

        if ($this->max_amount && $amount > $this->max_amount) {
            return false;
        }

        return true;
    }
}
