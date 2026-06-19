<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\AreaEspecializacion;
use App\Modules\Fac\Models\HabilidadTecnica;
use App\Modules\Fac\Requests\StoreHabilidadTecnicaRequest;
use App\Modules\Fac\Requests\UpdateHabilidadTecnicaRequest;
use Illuminate\Http\Request;

class HabilidadTecnicaController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $habilidadesTecnicas = HabilidadTecnica::with('areaEspecializacion')
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%")
                    ->orWhereHas('areaEspecializacion', fn ($area) => $area->where('nombre', 'like', "%{$buscar}%"));
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.habilidad-tecnica.index', compact('habilidadesTecnicas', 'buscar'));
    }

    public function create()
    {
        $areas = AreaEspecializacion::where('activo', true)->orderBy('nombre')->get();
        return view('fac.catalogos.habilidad-tecnica.create', compact('areas'));
    }

    public function store(StoreHabilidadTecnicaRequest $request)
    {
        HabilidadTecnica::create([
            'id_area_especializacion' => $request->id_area_especializacion,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => $request->boolean('activo', true),
            'usuario_crea' => auth()->id(),
        ]);

        return redirect()->route('fac.catalogos.habilidad-tecnica.index')
            ->with('success', 'Habilidad técnica creada correctamente.');
    }

    public function edit(HabilidadTecnica $habilidadTecnica)
    {
        $areas = AreaEspecializacion::where('activo', true)->orderBy('nombre')->get();
        return view('fac.catalogos.habilidad-tecnica.edit', compact('habilidadTecnica', 'areas'));
    }

    public function update(UpdateHabilidadTecnicaRequest $request, HabilidadTecnica $habilidadTecnica)
    {
        $habilidadTecnica->update([
            'id_area_especializacion' => $request->id_area_especializacion,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => $request->boolean('activo'),
            'usuario_mod' => auth()->id(),
        ]);

        return redirect()->route('fac.catalogos.habilidad-tecnica.index')
            ->with('success', 'Habilidad técnica actualizada correctamente.');
    }

    public function destroy(HabilidadTecnica $habilidadTecnica)
    {
        $habilidadTecnica->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $habilidadTecnica->delete();

        return redirect()->route('fac.catalogos.habilidad-tecnica.index')
            ->with('success', 'Habilidad técnica eliminada correctamente.');
    }
}
