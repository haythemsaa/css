<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePartnerReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only premium and socios users can review
        return in_array($this->user()->user_type, ['premium', 'socios', 'admin']);
    }

    public function rules(): array
    {
        return [
            'partner_id' => 'required|exists:partners,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'partner_id.required' => 'Le partenaire est obligatoire',
            'partner_id.exists' => 'Ce partenaire n\'existe pas',
            'rating.required' => 'La note est obligatoire',
            'rating.min' => 'La note minimale est 1',
            'rating.max' => 'La note maximale est 5',
            'comment.required' => 'Le commentaire est obligatoire',
            'comment.min' => 'Le commentaire doit contenir au moins 10 caractères',
            'comment.max' => 'Le commentaire ne peut pas dépasser 1000 caractères',
        ];
    }
}
