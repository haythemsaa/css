<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'campaign_id' => 'required|exists:campaigns,id',
            'amount' => 'required|numeric|min:5|max:10000',
            'payment_method' => 'required|string|in:d17,konnect,paymee,sadad,stripe',
            'is_anonymous' => 'nullable|boolean',
            'message' => 'nullable|string|max:500',
            'payment_details' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'campaign_id.required' => 'La campagne est obligatoire',
            'campaign_id.exists' => 'Cette campagne n\'existe pas',
            'amount.required' => 'Le montant est obligatoire',
            'amount.min' => 'Le montant minimum est de 5 TND',
            'amount.max' => 'Le montant maximum est de 10 000 TND',
            'payment_method.required' => 'La méthode de paiement est obligatoire',
            'payment_method.in' => 'Méthode de paiement invalide',
        ];
    }
}
