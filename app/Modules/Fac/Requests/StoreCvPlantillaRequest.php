<?php

namespace App\Modules\Fac\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCvPlantillaRequest extends FormRequest
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
        return [
            'codigo' => [
                'required',
                'string',
                'max:80',
                'alpha_dash',
                Rule::unique('tbl_cv_plantilla', 'codigo')->whereNull('deleted_at'),
            ],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'tamanio_papel' => ['required', Rule::in(['letter', 'a4', 'legal'])],
            'orientacion' => ['required', Rule::in(['portrait', 'landscape'])],
        ];
    }
}