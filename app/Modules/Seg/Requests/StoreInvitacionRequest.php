<?php

namespace App\Modules\Seg\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvitacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'duracion_ilimitada' => $this->boolean('duracion_ilimitada'),
            'usos_ilimitados' => $this->boolean('usos_ilimitados'),
        ]);
    }

    public function rules(): array
    {
        return [
            'duracion_ilimitada' => [
                'boolean',
            ],

            'duracion_horas' => [
                'nullable',
                Rule::requiredIf(
                    fn () => ! $this->boolean('duracion_ilimitada')
                ),
                'integer',
                'min:1',
                'max:8760',
            ],

            'usos_ilimitados' => [
                'boolean',
            ],

            'max_usos' => [
                'nullable',
                Rule::requiredIf(
                    fn () => ! $this->boolean('usos_ilimitados')
                ),
                'integer',
                'min:1',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'duracion_horas.required' =>
                'Debe indicar la duración de la invitación o seleccionar duración ilimitada.',

            'duracion_horas.integer' =>
                'La duración debe expresarse en horas enteras.',

            'duracion_horas.min' =>
                'La duración mínima es de una hora.',

            'duracion_horas.max' =>
                'La duración máxima permitida es de 8760 horas.',

            'max_usos.required' =>
                'Debe indicar el máximo de usos o seleccionar usos ilimitados.',

            'max_usos.integer' =>
                'El máximo de usos debe ser un número entero.',

            'max_usos.min' =>
                'El máximo de usos debe ser al menos uno.',

            'max_usos.max' =>
                'El máximo de usos permitido es 1000.',
        ];
    }
}