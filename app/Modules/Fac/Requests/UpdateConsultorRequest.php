<?php

namespace App\Modules\Fac\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConsultorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $consultor = $this->route('consultor');
        $idConsultor = $consultor?->id_consultor;

        return [
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'apellido_casa' => ['nullable', 'string', 'max:100'],
            'estado_civil' => ['nullable', 'string', 'max:20'],
            'nacionalidad' => ['nullable', 'string', 'max:50'],

            'tipo_identificacion' => ['nullable', 'string', 'max:30'],
            'numero_identificacion' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('tbl_consultor', 'numero_identificacion')
                    ->ignore($idConsultor, 'id_consultor')
                    ->whereNull('deleted_at'),
            ],

            'nit' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('tbl_consultor', 'nit')
                    ->ignore($idConsultor, 'id_consultor')
                    ->whereNull('deleted_at'),
            ],

            'nrc' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('tbl_consultor', 'nrc')
                    ->ignore($idConsultor, 'id_consultor')
                    ->whereNull('deleted_at'),
            ],

            'sexo' => ['nullable', 'in:M,F'],
            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],

            'id_pais' => ['nullable', 'integer', 'exists:tbl_pais,id_pais'],
            'id_departamento' => ['nullable', 'integer', 'exists:tbl_departamento,id_departamento'],
            'id_municipio_mh' => ['nullable', 'integer', 'exists:tbl_municipio_mh,id_municipio_mh'],
            'id_municipio' => ['nullable', 'integer', 'exists:tbl_municipio,id_municipio'],

            'direccion_residencia' => ['nullable', 'string', 'max:200'],

            'foto' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'vigente' => ['nullable', 'boolean'],
            'activo' => ['nullable', 'boolean'],

            'documento_identificacion' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:5120',
            ],

            'documento_nit' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:5120',
            ],

            'documento_nrc' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:5120',
            ],

            'actividad_giro' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombres.required' => 'Debes ingresar los nombres del consultor.',
            'apellidos.required' => 'Debes ingresar los apellidos del consultor.',
            'numero_identificacion.unique' => 'Ya existe otro consultor con este número de identificación.',
            'nit.unique' => 'Ya existe otro consultor con este NIT.',
            'nrc.unique' => 'Ya existe otro consultor con este NRC.',
            'sexo.in' => 'El sexo seleccionado no es válido.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a la fecha actual.',
            'foto.image' => 'La foto debe ser una imagen válida.',
            'foto.mimes' => 'La foto debe ser JPG, JPEG, PNG o WEBP.',
            'foto.max' => 'La foto no debe superar los 2 MB.',
            'documento_identificacion.mimes' => 'El documento de identificación debe ser PDF, JPG, JPEG, PNG o WEBP.',
            'documento_identificacion.max' => 'El documento de identificación no debe superar los 5 MB.',

            'documento_nit.mimes' => 'El documento NIT debe ser PDF, JPG, JPEG, PNG o WEBP.',
            'documento_nit.max' => 'El documento NIT no debe superar los 5 MB.',

            'documento_nrc.mimes' => 'El documento NRC debe ser PDF, JPG, JPEG, PNG o WEBP.',
            'documento_nrc.max' => 'El documento NRC no debe superar los 5 MB.',

            'actividad_giro.max' => 'La actividad o giro no debe superar los 255 caracteres.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validarRelacionGeografica($validator);
        });
    }

    private function validarRelacionGeografica($validator): void
    {
        $idPais = $this->input('id_pais');
        $idDepartamento = $this->input('id_departamento');
        $idMunicipioMh = $this->input('id_municipio_mh');
        $idMunicipio = $this->input('id_municipio');

        if ($idDepartamento && $idPais) {
            $existe = \DB::table('tbl_departamento')
                ->where('id_departamento', $idDepartamento)
                ->where('id_pais', $idPais)
                ->exists();

            if (!$existe) {
                $validator->errors()->add('id_departamento', 'El departamento no pertenece al país seleccionado.');
            }
        }

        if ($idMunicipioMh && $idDepartamento) {
            $existe = \DB::table('tbl_municipio_mh')
                ->where('id_municipio_mh', $idMunicipioMh)
                ->where('id_departamento', $idDepartamento)
                ->exists();

            if (!$existe) {
                $validator->errors()->add('id_municipio_mh', 'El municipio MH no pertenece al departamento seleccionado.');
            }
        }

        if ($idMunicipio) {
            $query = \DB::table('tbl_municipio')
                ->where('id_municipio', $idMunicipio);

            if ($idPais) {
                $query->where('id_pais', $idPais);
            }

            if ($idDepartamento) {
                $query->where('id_departamento', $idDepartamento);
            }

            if ($idMunicipioMh) {
                $query->where('id_municipio_mh', $idMunicipioMh);
            }

            if (!$query->exists()) {
                $validator->errors()->add('id_municipio', 'El municipio/distrito no corresponde a la ubicación seleccionada.');
            }
        }
    }
}