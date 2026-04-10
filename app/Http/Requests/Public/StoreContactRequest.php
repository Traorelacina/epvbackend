<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'       => 'required|string|max:150',
            'email'     => 'required|email|max:255',
            'telephone' => 'nullable|string|max:30',
            'sujet'     => 'required|string|max:255',
            'message'   => 'required|string|min:20|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'     => 'Votre nom est obligatoire.',
            'email.required'   => 'Votre adresse email est obligatoire.',
            'email.email'      => 'L\'adresse email n\'est pas valide.',
            'sujet.required'   => 'Le sujet est obligatoire.',
            'message.required' => 'Le message est obligatoire.',
            'message.min'      => 'Le message doit contenir au moins 20 caractères.',
            'message.max'      => 'Le message ne peut pas dépasser 2000 caractères.',
        ];
    }
}