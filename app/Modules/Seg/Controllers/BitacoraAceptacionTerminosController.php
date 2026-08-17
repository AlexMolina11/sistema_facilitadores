<?php

namespace App\Modules\Seg\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Seg\Models\AceptacionTerminos;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BitacoraAceptacionTerminosController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = trim(
            (string) $request->get('buscar')
        );

        $version = trim(
            (string) $request->get('version')
        );

        $aceptaciones = AceptacionTerminos::query()
            ->with([
                'usuario',
                'consultor',
                'invitacion',
            ])

            ->when(
                $buscar !== '',
                function ($query) use ($buscar) {
                    $like = "%{$buscar}%";

                    $query->where(function ($q) use ($like) {

                        $q->whereHas(
                            'consultor',
                            function ($consultor) use ($like) {
                                $consultor
                                    ->where('nombres', 'like', $like)
                                    ->orWhere('apellidos', 'like', $like)
                                    ->orWhere(
                                        'numero_identificacion',
                                        'like',
                                        $like
                                    );
                            }
                        )

                        ->orWhereHas(
                            'usuario',
                            function ($usuario) use ($like) {
                                $usuario
                                    ->where('email', 'like', $like);
                            }
                        );
                    });
                }
            )

            ->when(
                $version !== '',
                fn ($query) =>
                    $query->where(
                        'version_terminos',
                        $version
                    )
            )

            ->orderByDesc('fecha_aceptacion')
            ->paginate(15)
            ->withQueryString();

        $versiones = AceptacionTerminos::query()
            ->select('version_terminos')
            ->distinct()
            ->orderByDesc('version_terminos')
            ->pluck('version_terminos');

        return view(
            'seg.bitacora-terminos.index',
            compact(
                'aceptaciones',
                'buscar',
                'version',
                'versiones'
            )
        );
    }
}