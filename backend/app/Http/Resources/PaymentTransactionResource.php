<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_id' => $this->transaction_id,
            'user_id' => $this->user_id,
            'payment_method' => new PaymentMethodResource($this->whenLoaded('paymentMethod')),
            'payable_type' => $this->payable_type,
            'payable_id' => $this->payable_id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'transaction_fee' => $this->transaction_fee,
            'net_amount' => $this->net_amount,
            'status' => $this->status,
            'payment_gateway' => $this->payment_gateway,
            'gateway_transaction_id' => $this->gateway_transaction_id,
            'gateway_reference' => $this->gateway_reference,
            'failure_reason' => $this->failure_reason,
            'processed_at' => $this->processed_at,
            'completed_at' => $this->completed_at,
            'refunded_at' => $this->refunded_at,
            'refund_reference' => $this->refund_reference,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
