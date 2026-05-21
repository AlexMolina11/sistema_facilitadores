<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\TipoHabilidad;
use App\Modules\Fac\Requests\StoreTipoHabilidadRequest;
use App\Modules\Fac\Requests\UpdateTipoHabilidadRequest;
use Illuminate\Http\Request;

class TipoHabilidadController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $tiposHabilidad = TipoHabilidad::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.tipo-habilidad.index', compact('tiposHabilidad', 'buscar'));
    }

    public function create()
    {
        return view('fac.catalogos.tipo-habilidad.create');
    }

    public function store(StoreTipoHabilidadRequest $request)
    {
        TipoHabilidad::create([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_crea' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.tipo-habilidad.index')
            ->with('success', 'Tipo de habilidad creado correctamente.');
    }

    public function edit(TipoHabilidad $tipoHabilidad)
    {
        return view('fac.catalogos.tipo-habilidad.edit', compact('tipoHabilidad'));
    }

    public function update(UpdateTipoHabilidadRequest $request, TipoHabilidad $tipoHabilidad)
    {
        $tipoHabilidad->update([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_mod' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.tipo-habilidad.index')
            ->with('success', 'Tipo de habilidad actualizado correctamente.');
    }

    public function destroy(TipoHabilidad $tipoHabilidad)
    {
        $tipoHabilidad->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $tipoHabilidad->delete();

        return redirect()
            ->route('fac.catalogos.tipo-habilidad.index')
            ->with('success', 'Tipo de habilidad eliminado correctamente.');
    }
}