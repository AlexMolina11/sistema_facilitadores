<?php

namespace App\Modules\Seg\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Seg\Models\Usuario;
use App\Modules\Seg\Requests\LoginRequest;
use App\Modules\Seg\Services\BitacoraAccesoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly BitacoraAccesoService $bitacora
    ) {
    }

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            /** @var Usuario $usuario */
            $usuario = Auth::user();

            /*
            |--------------------------------------------------------------------------
            | Usuarios con acceso administrativo
            |--------------------------------------------------------------------------
            |
            | Un Administrador o Gestor puede tener además id_consultor.
            | Si posee acceso al Dashboard, este continúa siendo su punto
            | principal de entrada.
            |
            */
            if ($usuario->tienePermiso('fac.dashboard.ver')) {
                return redirect()
                    ->route('fac.dashboard');
            }

            /*
            |--------------------------------------------------------------------------
            | Usuario con perfil de consultor
            |--------------------------------------------------------------------------
            */
            if ($usuario->id_consultor) {
                return redirect()
                    ->route('fac.mi-perfil');
            }

            abort(
                403,
                'El usuario no posee una ruta de inicio habilitada.'
            );
        }

        return view('seg.auth.login');
    }

    public function login(
        LoginRequest $request
    ): RedirectResponse {
        $request->ensureIsNotRateLimited();

        $usuario = Usuario::where(
            'email',
            $request->email
        )->first();

        if (
            ! $usuario
            || ! Hash::check(
                $request->password,
                $usuario->password
            )
        ) {
            $request->hitRateLimiter();

            $this->bitacora->seguridadLoginFallido(
                $usuario?->id_usuario,
                $request
            );

            return back()
                ->withErrors([
                    'email' =>
                        'Las credenciales ingresadas no son válidas.',
                ])
                ->onlyInput('email');
        }

        if (! $usuario->activo) {
            $request->hitRateLimiter();

            $this->bitacora->seguridadUsuarioInactivo(
                $usuario->id_usuario,
                $request
            );

            return back()
                ->withErrors([
                    'email' =>
                        'El usuario se encuentra inactivo.',
                ])
                ->onlyInput('email');
        }

        $request->clearRateLimiter();

        Auth::login(
            $usuario,
            $request->boolean('remember')
        );

        $request->session()->regenerate();

        $usuario
            ->forceFill([
                'ultimo_acceso' => now(),
            ])
            ->save();

        $this->bitacora->seguridadLoginExitoso(
            $usuario->id_usuario,
            $request
        );

        /*
        |--------------------------------------------------------------------------
        | Prioridad de entrada
        |--------------------------------------------------------------------------
        |
        | 1. Usuario con Dashboard:
        |    Administrador / Gestor según permisos.
        |
        | 2. Usuario sin Dashboard pero con perfil Consultor:
        |    Mi perfil.
        |
        | Tener id_consultor NO elimina ni sustituye los permisos del rol.
        |
        */

        if ($usuario->tienePermiso('fac.dashboard.ver')) {
            return redirect()
                ->intended(
                    route('fac.dashboard')
                );
        }

        if ($usuario->id_consultor) {
            return redirect()
                ->route('fac.mi-perfil');
        }

        abort(
            403,
            'El usuario fue autenticado, pero no posee una ruta de inicio habilitada.'
        );
    }

    public function logout(
        Request $request
    ): RedirectResponse {
        $idUsuario = Auth::id();

        $this->bitacora->seguridadLogout(
            $idUsuario,
            $request
        );

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Sesión cerrada correctamente.'
            );
    }
}