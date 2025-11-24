<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProposeTradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'offered_user_card_id' => 'required|exists:user_cards,id',
            'requested_user_card_id' => 'required|exists:user_cards,id|different:offered_user_card_id',
            'message' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'offered_user_card_id.required' => 'Vous devez sélectionner une carte à offrir',
            'offered_user_card_id.exists' => 'Cette carte n\'existe pas',
            'requested_user_card_id.required' => 'Vous devez sélectionner une carte demandée',
            'requested_user_card_id.exists' => 'Cette carte n\'existe pas',
            'requested_user_card_id.different' => 'Vous ne pouvez pas échanger la même carte',
            'message.max' => 'Le message ne peut pas dépasser 500 caractères',
        ];
    }
}
