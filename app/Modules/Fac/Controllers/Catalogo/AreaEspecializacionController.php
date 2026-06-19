<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\AreaEspecializacion;
use App\Modules\Fac\Requests\StoreAreaEspecializacionRequest;
use App\Modules\Fac\Requests\UpdateAreaEspecializacionRequest;
use Illuminate\Http\Request;

class AreaEspecializacionController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $areas = AreaEspecializacion::query()
            ->when($buscar, fn ($query) => $query->where('nombre', 'like', "%{$buscar}%"))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.area-especializacion.index', compact('areas', 'buscar'));
    }

    public function create()
    {
        return view('fac.catalogos.area-especializacion.create');
    }

    public function store(StoreAreaEspecializacionRequest $request)
    {
        AreaEspecializacion::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => $request->boolean('activo', true),
            'usuario_crea' => auth()->id(),
        ]);

        return redirect()->route('fac.catalogos.area-especializacion.index')
            ->with('success', 'Área de especialización creada correctamente.');
    }

    public function edit(AreaEspecializacion $areaEspecializacion)
    {
        return view('fac.catalogos.area-especializacion.edit', compact('areaEspecializacion'));
    }

    public function update(UpdateAreaEspecializacionRequest $request, AreaEspecializacion $areaEspecializacion)
    {
        $areaEspecializacion->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => $request->boolean('activo'),
            'usuario_mod' => auth()->id(),
        ]);

        return redirect()->route('fac.catalogos.area-especializacion.index')
            ->with('success', 'Área de especialización actualizada correctamente.');
    }

    public function destroy(AreaEspecializacion $areaEspecializacion)
    {
        $areaEspecializacion->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $areaEspecializacion->delete();

        return redirect()->route('fac.catalogos.area-especializacion.index')
            ->with('success', 'Área de especialización eliminada correctamente.');
    }
}
