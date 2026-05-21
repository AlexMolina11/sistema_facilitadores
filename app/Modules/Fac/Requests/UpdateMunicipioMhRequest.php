<?php

namespace App\Modules\Fac\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMunicipioMhRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_departamento'     => ['required', 'integer', 'exists:tbl_departamento,id_departamento'],
            'municipio_mh_nombre' => ['required', 'string', 'max:100'],
            'mh_codigo_municipio' => ['nullable', 'string', 'max:50'],
            'activo'              => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'id_departamento'     => 'departamento',
            'municipio_mh_nombre' => 'nombre del municipio',
            'mh_codigo_municipio' => 'código MH del municipio',
            'activo'              => 'estado',
        ];
    }
}