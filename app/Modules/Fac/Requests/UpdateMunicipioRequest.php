<?php

namespace App\Modules\Fac\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMunicipioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_pais'            => ['required', 'integer', 'exists:tbl_pais,id_pais'],
            'id_departamento'    => [
                'required',
                'integer',
                Rule::exists('tbl_departamento', 'id_departamento')
                    ->where('id_pais', $this->id_pais),
            ],
            'id_municipio_mh'    => [
                'nullable',
                'integer',
                Rule::exists('tbl_municipio_mh', 'id_municipio_mh')
                    ->where('id_departamento', $this->id_departamento),
            ],
            'nombre_distrito'    => ['required', 'string', 'max:100'],
            'mh_codigo_distrito' => ['nullable', 'string', 'max:50'],
            'georeferencia'      => ['nullable', 'string', 'max:100'],
            'activo'             => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'id_pais'            => 'país',
            'id_departamento'    => 'departamento',
            'id_municipio_mh'    => 'municipio MH',
            'nombre_distrito'    => 'nombre del municipio',
            'mh_codigo_distrito' => 'código MH del municipio',
            'georeferencia'      => 'georeferencia',
            'activo'             => 'estado',
        ];
    }
}