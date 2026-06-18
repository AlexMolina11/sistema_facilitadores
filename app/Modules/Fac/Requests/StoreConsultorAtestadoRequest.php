<?php

namespace App\Modules\Fac\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConsultorAtestadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_tipo_formacion' => ['required', 'integer', 'exists:tbl_tipo_formacion,id_tipo_formacion'],
            'id_tipo_atestado' => [
                'nullable',
                'integer',
                Rule::exists('tbl_tipo_atestado', 'id_tipo_atestado')
                    ->where('activo', true)
                    ->where('id_tipo_formacion', $this->input('id_tipo_formacion')),
            ],
            'id_nivel_academico' => ['nullable', 'integer', 'exists:tbl_nivel_academico,id_nivel_academico'],
            'id_pais' => ['nullable', 'integer', 'exists:tbl_pais,id_pais'],
            'titulo' => ['required', 'string', 'max:250'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'institucion' => ['nullable', 'string', 'max:250'],
            'entidad_acreditadora' => ['nullable', 'string', 'max:250'],
            'cliente_institucion' => ['nullable', 'string', 'max:250'],
            'codigo_acreditacion' => ['nullable', 'string', 'max:100'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'fecha_emision' => ['nullable', 'date'],
            'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:fecha_emision'],
            'horas' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'archivo_atestado' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_tipo_formacion.required' => 'Debes seleccionar el tipo de formación.',
            'id_tipo_atestado.exists' => 'El tipo de atestado no pertenece al tipo de formación seleccionado.',
            'titulo.required' => 'Debes ingresar el título o nombre del registro.',
            'fecha_fin.after_or_equal' => 'La fecha de fin no puede ser menor que la fecha de inicio.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento no puede ser menor que la fecha de emisión.',
            'horas.integer' => 'Las horas deben ser un número entero.',
            'archivo_atestado.mimes' => 'El archivo debe ser PDF, JPG, JPEG, PNG o WEBP.',
            'archivo_atestado.max' => 'El archivo no debe superar los 5 MB.',
        ];
    }
}
