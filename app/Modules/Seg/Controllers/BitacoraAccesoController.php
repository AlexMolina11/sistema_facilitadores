<?php

namespace App\Modules\Seg\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Seg\Models\BitacoraAcceso;
use App\Modules\Seg\Models\Usuario;
use App\Modules\Seg\Services\BitacoraAccesoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BitacoraAccesoController extends Controller
{
    public function index(Request $request): View
    {
        $bitacoras = BitacoraAcceso::with('usuario')
            ->when($request->filled('tipo'), function ($q) use ($request) {
                $q->where('evento', 'like', $request->tipo . ':%');
            })
            ->when($request->filled('evento'), fn ($q) => $q->where('evento', $request->evento))
            ->when($request->filled('id_usuario'), fn ($q) => $q->where('id_usuario', $request->integer('id_usuario')))
            ->when($request->filled('ip'), fn ($q) => $q->where('ip', 'like', "%{$request->ip}%"))
            ->when($request->filled('q'), fn ($q) => $q->where(function ($sub) use ($request) {
                $sub->where('evento', 'like', "%{$request->q}%")
                    ->orWhere('ip', 'like', "%{$request->q}%")
                    ->orWhere('user_agent', 'like', "%{$request->q}%")
                    ->orWhereHas('usuario', function ($usuarioQuery) use ($request) {
                        $usuarioQuery->where('nombres', 'like', "%{$request->q}%")
                            ->orWhere('apellidos', 'like', "%{$request->q}%")
                            ->orWhere('email', 'like', "%{$request->q}%");
                    });
            }))
            ->when($request->filled('desde'), fn ($q) => $q->whereDate('fecha_evento', '>=', $request->desde))
            ->when($request->filled('hasta'), fn ($q) => $q->whereDate('fecha_evento', '<=', $request->hasta))
            ->latest('fecha_evento')
            ->paginate(20)
            ->withQueryString();

        $eventos = collect(BitacoraAccesoService::eventosBase())
            ->merge(BitacoraAcceso::query()->select('evento')->distinct()->pluck('evento'))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $tipos = $eventos
            ->map(fn ($evento) => str_contains($evento, ':') ? trim(str($evento)->before(':')->toString()) : null)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $usuarios = Usuario::query()
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->get(['id_usuario', 'nombres', 'apellidos', 'email']);

        return view('seg.bitacora.index', compact('bitacoras', 'eventos', 'tipos', 'usuarios'));
    }
}
