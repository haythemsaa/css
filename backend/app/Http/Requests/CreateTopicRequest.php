<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:forum_categories,id',
            'title' => 'required|string|min:5|max:255',
            'content' => 'required|string|min:20|max:10000',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'La catégorie est obligatoire',
            'category_id.exists' => 'Cette catégorie n\'existe pas',
            'title.required' => 'Le titre est obligatoire',
            'title.min' => 'Le titre doit contenir au moins 5 caractères',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères',
            'content.required' => 'Le contenu est obligatoire',
            'content.min' => 'Le contenu doit contenir au moins 20 caractères',
            'content.max' => 'Le contenu ne peut pas dépasser 10 000 caractères',
        ];
    }
}
