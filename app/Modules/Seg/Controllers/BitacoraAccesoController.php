<?php

namespace App\Modules\Seg\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Seg\Models\BitacoraAcceso;
use App\Modules\Seg\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BitacoraAccesoController extends Controller
{
    public function index(Request $request): View
    {
        $bitacoras = BitacoraAcceso::with('usuario')
            ->when($request->filled('tipo'), function ($q) use ($request) {
                $q->where('evento', 'like', trim($request->tipo) . ':%');
            })
            ->when($request->filled('evento'), function ($q) use ($request) {
                $q->whereRaw('TRIM(evento) = ?', [trim($request->evento)]);
            })
            ->when($request->filled('id_usuario'), fn ($q) => $q->where('id_usuario', $request->integer('id_usuario')))
            ->when($request->filled('ip'), fn ($q) => $q->where('ip', 'like', '%' . trim($request->ip) . '%'))
            ->when($request->filled('q'), fn ($q) => $q->where(function ($sub) use ($request) {
                $buscar = trim($request->q);

                $sub->where('evento', 'like', "%{$buscar}%")
                    ->orWhere('ip', 'like', "%{$buscar}%")
                    ->orWhere('user_agent', 'like', "%{$buscar}%")
                    ->orWhereHas('usuario', function ($usuarioQuery) use ($buscar) {
                        $usuarioQuery->where('nombres', 'like', "%{$buscar}%")
                            ->orWhere('apellidos', 'like', "%{$buscar}%")
                            ->orWhere('email', 'like', "%{$buscar}%");
                    });
            }))
            ->when($request->filled('desde'), fn ($q) => $q->whereDate('fecha_evento', '>=', $request->desde))
            ->when($request->filled('hasta'), fn ($q) => $q->whereDate('fecha_evento', '<=', $request->hasta))
            ->latest('fecha_evento')
            ->paginate(20)
            ->withQueryString();

        $eventos = BitacoraAcceso::query()
            ->whereNotNull('evento')
            ->selectRaw('TRIM(evento) as evento')
            ->distinct()
            ->orderBy('evento')
            ->pluck('evento');

        $tipos = $eventos
            ->map(fn ($evento) => str_contains($evento, ':') ? trim(Str::before($evento, ':')) : 'General')
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