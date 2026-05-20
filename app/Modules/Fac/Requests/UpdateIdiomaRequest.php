<?php

namespace App\Modules\Fac\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIdiomaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'activo' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre del idioma',
            'activo' => 'estado',
        ];
    }
}