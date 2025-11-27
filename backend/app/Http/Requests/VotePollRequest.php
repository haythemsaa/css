<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VotePollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'option_index' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'option_index.required' => 'L\'option est obligatoire',
            'option_index.integer' => 'L\'option doit être un nombre',
            'option_index.min' => 'L\'option est invalide',
        ];
    }
}
