<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHoraireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'niveau'      => 'sometimes|string|max:150',
            'periode'     => 'sometimes|string|max:150',
            'heure_debut' => 'sometimes|date_format:H:i',
            'heure_fin'   => 'sometimes|date_format:H:i',
            'jours'       => 'nullable|string|max:100',
            'notes'       => 'nullable|string',
        ];
    }
}