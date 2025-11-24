<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuyTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:1|max:10',
            'payment_method' => 'required|string|in:d17,konnect,paymee,sadad,stripe',
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'La quantité est obligatoire',
            'quantity.min' => 'Vous devez acheter au moins 1 ticket',
            'quantity.max' => 'Vous ne pouvez pas acheter plus de 10 tickets à la fois',
            'payment_method.required' => 'La méthode de paiement est obligatoire',
            'payment_method.in' => 'Méthode de paiement invalide',
        ];
    }
}
