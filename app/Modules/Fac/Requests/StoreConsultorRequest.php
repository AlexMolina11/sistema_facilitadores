<?php

namespace App\Modules\Fac\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'apellido_casa' => ['nullable', 'string', 'max:100'],
            'estado_civil' => ['nullable', 'string', 'max:20'],
            'nacionalidad' => ['nullable', 'string', 'max:50'],
            'tipo_identificacion' => ['nullable', 'string', 'max:30'],
            'numero_identificacion' => ['nullable', 'string', 'max:30'],
            'nit' => ['nullable', 'string', 'max:20'],
            'nrc' => ['nullable', 'string', 'max:20'],
            'sexo' => ['nullable', 'in:M,F'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'id_pais' => ['nullable', 'integer', 'exists:tbl_pais,id_pais'],
            'id_municipio' => ['nullable', 'integer', 'exists:tbl_municipio,id_municipio'],
            'direccion_residencia' => ['nullable', 'string', 'max:200'],
            'emergencia_contacto' => ['nullable', 'string', 'max:150'],
            'vigente' => ['nullable', 'boolean'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}