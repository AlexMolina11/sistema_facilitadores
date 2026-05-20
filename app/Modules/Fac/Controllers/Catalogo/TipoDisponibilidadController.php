<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\TipoDisponibilidad;
use App\Modules\Fac\Requests\StoreTipoDisponibilidadRequest;
use App\Modules\Fac\Requests\UpdateTipoDisponibilidadRequest;
use Illuminate\Http\Request;

class TipoDisponibilidadController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $tipos = TipoDisponibilidad::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.tipo-disponibilidad.index', compact('tipos', 'buscar'));
    }

    public function create()
    {
        return view('fac.catalogos.tipo-disponibilidad.create');
    }

    public function store(StoreTipoDisponibilidadRequest $request)
    {
        TipoDisponibilidad::create([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_crea' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.tipo-disponibilidad.index')
            ->with('success', 'Tipo de disponibilidad creado correctamente.');
    }

    public function edit(TipoDisponibilidad $tipo_disponibilidad)
    {
        return view('fac.catalogos.tipo-disponibilidad.edit', compact('tipo_disponibilidad'));
    }

    public function update(UpdateTipoDisponibilidadRequest $request, TipoDisponibilidad $tipo_disponibilidad)
    {
        $tipo_disponibilidad->update([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_mod' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.tipo-disponibilidad.index')
            ->with('success', 'Tipo de disponibilidad actualizado correctamente.');
    }

    public function destroy(TipoDisponibilidad $tipo_disponibilidad)
    {
        $tipo_disponibilidad->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $tipo_disponibilidad->delete();

        return redirect()
            ->route('fac.catalogos.tipo-disponibilidad.index')
            ->with('success', 'Tipo de disponibilidad eliminado correctamente.');
    }
}