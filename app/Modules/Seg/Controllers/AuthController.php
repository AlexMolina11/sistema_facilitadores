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
            return redirect()->route('fac.dashboard');
        }

        return view('seg.auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            $this->bitacora->registrar($usuario?->id_usuario, 'login_fallido', $request);

            return back()
                ->withErrors(['email' => 'Las credenciales ingresadas no son válidas.'])
                ->onlyInput('email');
        }

        if (!$usuario->activo) {
            $this->bitacora->registrar($usuario->id_usuario, 'login_usuario_inactivo', $request);

            return back()
                ->withErrors(['email' => 'El usuario se encuentra inactivo.'])
                ->onlyInput('email');
        }

        Auth::login($usuario, $request->boolean('remember'));
        $request->session()->regenerate();

        $usuario->forceFill(['ultimo_acceso' => now()])->save();
        $this->bitacora->registrar($usuario->id_usuario, 'login_exitoso', $request);

        return redirect()->intended(route('fac.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $idUsuario = Auth::id();
        $this->bitacora->registrar($idUsuario, 'logout', $request);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}
