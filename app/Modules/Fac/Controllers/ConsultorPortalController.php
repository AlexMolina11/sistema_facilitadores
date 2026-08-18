<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultorPortalController extends Controller
{
    /**
     * Redirige al expediente del consultor autenticado.
     */
    public function perfil(Request $request): RedirectResponse
    {
        $usuario = $request->user();

        if (! $usuario || ! $usuario->id_consultor) {
            abort(
                403,
                'Tu usuario no posee un perfil de consultor asociado.'
            );
        }

        return redirect()->route(
            'fac.consultores.show',
            $usuario->id_consultor
        );
    }

    /**
     * Redirige directamente a la edición del propio expediente.
     */
    public function editarPerfil(Request $request): RedirectResponse
    {
        $usuario = $request->user();

        if (! $usuario || ! $usuario->id_consultor) {
            abort(
                403,
                'Tu usuario no posee un perfil de consultor asociado.'
            );
        }

        return redirect()->route(
            'fac.consultores.edit',
            $usuario->id_consultor
        );
    }

    /**
     * Historial de capacitaciones FEPADE.
     * Exclusivamente de lectura.
     */
    public function capacitaciones(Request $request): View
    {
        $usuario = $request->user();

        if (! $usuario || ! $usuario->id_consultor) {
            abort(
                403,
                'Tu usuario no posee un perfil de consultor asociado.'
            );
        }

        $consultor = $usuario
            ->consultor()
            ->with([
                'capacitacionesFepade' => fn ($query) =>
                    $query
                        ->where('activo', true)
                        ->orderByDesc('fecha_inicio')
                        ->orderByDesc('id_capacitacion_fepade'),
            ])
            ->firstOrFail();

        return view(
            'fac.consultor-portal.capacitaciones',
            compact('consultor')
        );
    }
}