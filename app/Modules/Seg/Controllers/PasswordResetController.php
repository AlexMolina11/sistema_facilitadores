<?php

namespace App\Modules\Seg\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Seg\Models\Usuario;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Muestra el formulario donde el usuario solicita
     * el enlace para restablecer su contraseña.
     */
    public function showForgotPassword(): View
    {
        return view('seg.auth.forgot-password');
    }

    /**
     * Envía el enlace de recuperación al correo indicado.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => [
                'required',
                'email',
            ],
        ], [
            'email.required' => 'Debes ingresar tu correo electrónico.',
            'email.email' => 'Debes ingresar un correo electrónico válido.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validación del usuario
        |--------------------------------------------------------------------------
        |
        | No permitimos recuperación para usuarios eliminados o inactivos.
        |
        */

        $usuario = Usuario::query()
            ->where('email', $request->email)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Respuesta deliberadamente genérica
        |--------------------------------------------------------------------------
        |
        | Evitamos revelar públicamente si un correo pertenece o no a un
        | usuario del sistema.
        |
        */

        if (!$usuario || !$usuario->activo) {
            return back()->with(
                'status',
                'Si el correo ingresado pertenece a una cuenta activa, recibirás un enlace para restablecer tu contraseña.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Laravel Password Broker
        |--------------------------------------------------------------------------
        */

        $status = Password::sendResetLink([
            'email' => $request->email,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Respuesta
        |--------------------------------------------------------------------------
        */

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(
                'status',
                'Si el correo ingresado pertenece a una cuenta activa, recibirás un enlace para restablecer tu contraseña.'
            );
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => $this->passwordBrokerMessage($status),
            ]);
    }

    /**
     * Muestra el formulario para establecer la nueva contraseña.
     */
    public function showResetPassword(
        Request $request,
        string $token
    ): View {
        return view('seg.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Guarda la nueva contraseña.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => [
                'required',
            ],

            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'token.required' => 'El enlace de recuperación no es válido.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',

            'password.required' => 'Debes ingresar una nueva contraseña.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validación adicional del usuario
        |--------------------------------------------------------------------------
        */

        $usuario = Usuario::query()
            ->where('email', $request->email)
            ->first();

        if (!$usuario || !$usuario->activo) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'No fue posible restablecer la contraseña.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Restablecimiento
        |--------------------------------------------------------------------------
        |
        | IMPORTANTE:
        |
        | No utilizamos remember_token porque seg_usuarios no posee esa
        | columna.
        |
        | El modelo Usuario ya tiene:
        |
        |     'password' => 'hashed'
        |
        | por lo que basta asignar la nueva contraseña.
        |
        */

        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function (Usuario $usuario, string $password) {
                $usuario->password = $password;
                $usuario->save();

                event(new PasswordReset($usuario));
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Resultado exitoso
        |--------------------------------------------------------------------------
        */

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'Tu contraseña fue actualizada correctamente. Ya puedes iniciar sesión.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Error
        |--------------------------------------------------------------------------
        */

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => $this->passwordBrokerMessage($status),
            ]);
    }

    /**
     * Traduce los estados principales del Password Broker
     * a mensajes apropiados para el usuario.
     */
    private function passwordBrokerMessage(string $status): string
    {
        return match ($status) {
            Password::INVALID_USER =>
                'No fue posible procesar la solicitud de recuperación.',

            Password::INVALID_TOKEN =>
                'El enlace de recuperación no es válido o ya expiró.',

            Password::RESET_THROTTLED =>
                'Ya se solicitó recientemente un enlace de recuperación. Espera unos minutos antes de intentarlo nuevamente.',

            default =>
                'No fue posible procesar la recuperación de contraseña. Intenta nuevamente.',
        };
    }
}