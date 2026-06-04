<?php

namespace App\Modules\Seg\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Seg\Models\BitacoraAcceso;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BitacoraAccesoController extends Controller
{
    public function index(Request $request): View
    {
        $bitacoras = BitacoraAcceso::with('usuario')
            ->when($request->filled('evento'), fn ($q) => $q->where('evento', $request->evento))
            ->when($request->filled('ip'), fn ($q) => $q->where('ip', 'like', "%{$request->ip}%"))
            ->when($request->filled('desde'), fn ($q) => $q->whereDate('fecha_evento', '>=', $request->desde))
            ->when($request->filled('hasta'), fn ($q) => $q->whereDate('fecha_evento', '<=', $request->hasta))
            ->latest('fecha_evento')
            ->paginate(20)
            ->withQueryString();

        return view('seg.bitacora.index', compact('bitacoras'));
    }
}
