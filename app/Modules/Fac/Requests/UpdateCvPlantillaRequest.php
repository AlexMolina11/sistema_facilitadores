<?php

namespace App\Modules\Fac\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCvPlantillaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'codigo' => str($this->codigo)->slug('_')->toString(),
            'activa' => $this->boolean('activa'),
        ]);
    }

    public function rules(): array
    {
        $plantilla = $this->route('cvPlantilla');

        return [
            'codigo' => [
                'required',
                'string',
                'max:80',
                'alpha_dash',
                Rule::unique('tbl_cv_plantilla', 'codigo')
                    ->ignore($plantilla?->id_cv_plantilla, 'id_cv_plantilla')
                    ->whereNull('deleted_at'),
            ],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'tamanio_papel' => ['required', Rule::in(['letter', 'a4', 'legal'])],
            'orientacion' => ['required', Rule::in(['portrait', 'landscape'])],
            'orden' => ['required', 'integer', 'min:1'],
            'activa' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.unique' => 'Ya existe una plantilla con este código.',
            'codigo.alpha_dash' => 'El código solo puede contener letras, números, guiones y guiones bajos.',
        ];
    }
}