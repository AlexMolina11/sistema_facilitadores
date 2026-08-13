<?php

namespace App\Modules\Seg\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvitacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'duracion_ilimitada' => [
                'nullable',
                'boolean',
            ],

            'duracion_horas' => [
                'nullable',
                'integer',
                'min:1',
                'max:8760',
                'required_unless:duracion_ilimitada,1',
            ],

            'usos_ilimitados' => [
                'nullable',
                'boolean',
            ],

            'max_usos' => [
                'nullable',
                'integer',
                'min:1',
                'max:1000',
                'required_unless:usos_ilimitados,1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'duracion_horas.required_unless' =>
                'Debe indicar la duración de la invitación o seleccionar duración ilimitada.',

            'duracion_horas.integer' =>
                'La duración debe expresarse en horas enteras.',

            'duracion_horas.min' =>
                'La duración mínima es de una hora.',

            'duracion_horas.max' =>
                'La duración máxima permitida es de 8760 horas.',

            'max_usos.required_unless' =>
                'Debe indicar el máximo de usos o seleccionar usos ilimitados.',

            'max_usos.integer' =>
                'El máximo de usos debe ser un número entero.',

            'max_usos.min' =>
                'El máximo de usos debe ser al menos uno.',

            'max_usos.max' =>
                'El máximo de usos permitido es 1000.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'duracion_ilimitada' => $this->boolean('duracion_ilimitada'),
            'usos_ilimitados' => $this->boolean('usos_ilimitados'),
        ]);
    }
}