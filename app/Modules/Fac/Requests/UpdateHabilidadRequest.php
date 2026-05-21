<?php

namespace App\Modules\Fac\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHabilidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_tipo_habilidad' => ['required', 'exists:tbl_tipo_habilidad,id_tipo_habilidad'],
            'nombre' => ['required', 'string', 'max:150'],
            'activo' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'id_tipo_habilidad' => 'tipo de habilidad',
            'nombre' => 'nombre de la habilidad',
            'activo' => 'estado',
        ];
    }
}