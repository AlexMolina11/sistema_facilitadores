<?php

namespace App\Modules\Fac\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_pais'             => ['required', 'integer', 'exists:tbl_pais,id_pais'],
            'nombre_departamento' => ['required', 'string', 'max:100'],
            'mh_codigo_depto'     => ['nullable', 'string', 'max:50'],
            'georeferencia'       => ['nullable', 'string', 'max:100'],
            'activo'              => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'id_pais'             => 'país',
            'nombre_departamento' => 'nombre del departamento',
            'mh_codigo_depto'     => 'código MH del departamento',
            'georeferencia'       => 'georeferencia',
            'activo'              => 'estado',
        ];
    }
}