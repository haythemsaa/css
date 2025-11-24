<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name' => 'sometimes|string|max:255',
            'email' => "sometimes|string|email|max:255|unique:users,email,{$userId}",
            'phone' => "sometimes|string|max:20|unique:users,phone,{$userId}",
            'city' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'avatar' => 'nullable|image|max:2048',
            'bio' => 'nullable|string|max:500',
            'favorite_player_id' => 'nullable|exists:players,id',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Cet email est déjà utilisé',
            'phone.unique' => 'Ce numéro est déjà utilisé',
            'avatar.image' => 'Le fichier doit être une image',
            'avatar.max' => 'L\'image ne doit pas dépasser 2 Mo',
            'date_of_birth.before' => 'La date de naissance doit être antérieure à aujourd\'hui',
        ];
    }
}
