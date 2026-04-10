<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreHoraireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'niveau'      => 'required|string|max:150',
            'periode'     => 'required|string|max:150',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin'   => 'required|date_format:H:i|after:heure_debut',
            'jours'       => 'nullable|string|max:100',
            'notes'       => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'niveau.required'      => 'Le niveau est obligatoire.',
            'periode.required'     => 'La période est obligatoire.',
            'heure_debut.required' => 'L\'heure de début est obligatoire.',
            'heure_debut.date_format' => 'L\'heure de début doit être au format HH:MM.',
            'heure_fin.required'   => 'L\'heure de fin est obligatoire.',
            'heure_fin.date_format' => 'L\'heure de fin doit être au format HH:MM.',
            'heure_fin.after'      => 'L\'heure de fin doit être postérieure à l\'heure de début.',
        ];
    }
}