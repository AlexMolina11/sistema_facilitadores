<?php

namespace App\Modules\Seg\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
            'password.required' =>
                'Debes ingresar una contraseña.',

            'password.confirmed' =>
                'La confirmación de contraseña no coincide.',

            'acepta_terminos.accepted' =>
                'Debes leer y aceptar los términos y políticas de uso para continuar.',
        ];
    }
}