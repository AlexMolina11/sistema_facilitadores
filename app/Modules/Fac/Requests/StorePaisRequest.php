<?php

namespace App\Modules\Fac\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_pais'       => ['required', 'string', 'max:100'],
            'nombre_pais'       => ['required', 'string', 'max:100'],
            'mh_codigo_pais'    => ['nullable', 'string', 'max:50'],
            'mh_codigo_pais_new'=> ['nullable', 'string', 'max:50'],
            'activo'            => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo_pais'        => 'código del país',
            'nombre_pais'        => 'nombre del país',
            'mh_codigo_pais'     => 'código MH del país',
            'mh_codigo_pais_new' => 'código MH nuevo del país',
            'activo'             => 'estado',
        ];
    }
}