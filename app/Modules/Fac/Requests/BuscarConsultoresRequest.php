<?php

namespace App\Modules\Fac\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuscarConsultoresRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:150'],
            'fecha_filtro' => ['nullable', 'in:hoy,7_dias,30_dias,este_mes,mes_pasado,personalizado'],
            'fecha_desde' => ['nullable', 'date'],
            'fecha_hasta' => ['nullable', 'date', 'after_or_equal:fecha_desde'],
            'fecha_tipo' => ['nullable', 'in:actualizacion,creacion'],

            'sexo' => ['nullable', 'integer', 'exists:tbl_sexo,id_sexo'],
            'edad_min' => ['nullable', 'integer', 'min:18', 'max:100'],
            'edad_max' => ['nullable', 'integer', 'min:18', 'max:100', 'gte:edad_min'],

            'pais' => ['nullable', 'integer', 'exists:tbl_pais,id_pais'],
            'departamento' => ['nullable', 'integer', 'exists:tbl_departamento,id_departamento'],
            'municipio_mh' => ['nullable', 'integer', 'exists:tbl_municipio_mh,id_municipio_mh'],
            'distrito' => ['nullable', 'integer', 'exists:tbl_municipio,id_municipio'],
            'disponibilidad' => ['nullable', 'integer', 'exists:tbl_tipo_disponibilidad,id_tipo_disponibilidad'],

            'idioma' => ['nullable', 'integer', 'exists:tbl_idioma,id_idioma'],
            'nivel_idioma' => ['nullable', 'integer', 'exists:tbl_idioma_nivel,id_idioma_nivel'],

            'tipo_formacion' => ['nullable', 'integer', 'exists:tbl_tipo_formacion,id_tipo_formacion'],
            'nivel_academico' => ['nullable', 'integer', 'exists:tbl_nivel_academico,id_nivel_academico'],
            'tipo_atestado' => ['nullable', 'integer', 'exists:tbl_tipo_atestado,id_tipo_atestado'],

            'cargo' => ['nullable', 'string', 'max:120'],
            'empresa' => ['nullable', 'string', 'max:150'],
            'anios_experiencia' => ['nullable', 'integer', 'min:0', 'max:60'],

            'area_especializacion' => ['nullable', 'array'],
            'area_especializacion.*' => ['integer', 'exists:tbl_habilidad,id_habilidad'],
            'habilidades_tecnicas' => ['nullable', 'array'],
            'habilidades_tecnicas.*' => ['integer', 'exists:tbl_habilidad,id_habilidad'],
            'habilidades_blandas' => ['nullable', 'array'],
            'habilidades_blandas.*' => ['integer', 'exists:tbl_habilidad,id_habilidad'],
        ];
    }

    public function attributes(): array
    {
        return [
            'q' => 'búsqueda general',
            'fecha_desde' => 'fecha desde',
            'fecha_hasta' => 'fecha hasta',
            'edad_min' => 'edad mínima',
            'edad_max' => 'edad máxima',
            'municipio_mh' => 'municipio',
            'anios_experiencia' => 'años mínimos de experiencia',
        ];
    }
}