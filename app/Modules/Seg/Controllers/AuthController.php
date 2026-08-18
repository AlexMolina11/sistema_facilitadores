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
    public function __construct(private readonly BitacoraAccesoService $bitacora) {}

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            $usuario = Auth::user();

            if ($usuario->id_consultor) {
                return redirect()
                    ->route('fac.mi-perfil');
            }

            return redirect()
                ->route('fac.dashboard');
        }

        return view('seg.auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            $request->hitRateLimiter();
            $this->bitacora->seguridadLoginFallido($usuario?->id_usuario, $request);

            return back()
                ->withErrors(['email' => 'Las credenciales ingresadas no son válidas.'])
                ->onlyInput('email');
        }

        if (!$usuario->activo) {
            $request->hitRateLimiter();
            $this->bitacora->seguridadUsuarioInactivo($usuario->id_usuario, $request);

            return back()
                ->withErrors(['email' => 'El usuario se encuentra inactivo.'])
                ->onlyInput('email');
        }

        $request->clearRateLimiter();

        Auth::login($usuario, $request->boolean('remember'));
        $request->session()->regenerate();

        $usuario->forceFill(['ultimo_acceso' => now()])->save();
        $this->bitacora->seguridadLoginExitoso($usuario->id_usuario, $request);

        /*
        |--------------------------------------------------------------------------
        | Entrada especial para usuarios Consultor
        |--------------------------------------------------------------------------
        */

        if ($usuario->id_consultor) {
            return redirect()
                ->route('fac.mi-perfil');
        }

        return redirect()->intended(route('fac.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $idUsuario = Auth::id();
        $this->bitacora->seguridadLogout($idUsuario, $request);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}
