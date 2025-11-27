<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|min:5|max:5000',
            'parent_id' => 'nullable|exists:forum_replies,id',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Le contenu est obligatoire',
            'content.min' => 'Le contenu doit contenir au moins 5 caractères',
            'content.max' => 'Le contenu ne peut pas dépasser 5000 caractères',
            'parent_id.exists' => 'La réponse parente n\'existe pas',
        ];
    }
}
