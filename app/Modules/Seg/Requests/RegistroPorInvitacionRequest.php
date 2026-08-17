<?php

namespace App\Modules\Seg\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegistroPorInvitacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:150',

                Rule::unique(
                    'seg_usuarios',
                    'email'
                ),
            ],

            'password' => [
                'required',
                'confirmed',

                Password::min(8)
                    ->mixedCase()
                    ->numbers(),
            ],

            'acepta_terminos' => [
                'accepted',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' =>
                'Debes ingresar un correo electrónico.',

            'email.email' =>
                'El correo electrónico ingresado no tiene un formato válido.',

            'email.unique' =>
                'Ya existe una cuenta registrada con este correo electrónico.',

            'password.required' =>
                'Debes ingresar una contraseña.',

            'password.confirmed' =>
                'La confirmación de contraseña no coincide.',

            'acepta_terminos.accepted' =>
                'Debes leer y aceptar los términos y políticas de uso para continuar.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(
                trim(
                    (string) $this->input('email')
                )
            ),
        ]);
    }
}