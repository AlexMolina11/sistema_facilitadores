<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;

use App\Modules\Fac\Models\Habilidad;
use App\Modules\Fac\Models\TipoHabilidad;

use App\Modules\Fac\Requests\StoreHabilidadRequest;
use App\Modules\Fac\Requests\UpdateHabilidadRequest;

use Illuminate\Http\Request;

class HabilidadController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $habilidades = Habilidad::with('tipoHabilidad')
            ->when($buscar, function ($query) use ($buscar) {

                $query->where('nombre', 'like', "%{$buscar}%")
                    ->orWhereHas('tipoHabilidad', function ($q) use ($buscar) {
                        $q->where('nombre', 'like', "%{$buscar}%");
                    });

            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view(
            'fac.catalogos.habilidad.index',
            compact('habilidades', 'buscar')
        );
    }

    public function create()
    {
        $tiposHabilidad = TipoHabilidad::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'fac.catalogos.habilidad.create',
            compact('tiposHabilidad')
        );
    }

    public function store(StoreHabilidadRequest $request)
    {
        Habilidad::create([
            'id_tipo_habilidad' => $request->id_tipo_habilidad,
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_crea' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.habilidad.index')
            ->with('success', 'Habilidad creada correctamente.');
    }

    public function edit(Habilidad $habilidad)
    {
        $tiposHabilidad = TipoHabilidad::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'fac.catalogos.habilidad.edit',
            compact('habilidad', 'tiposHabilidad')
        );
    }

    public function update(
        UpdateHabilidadRequest $request,
        Habilidad $habilidad
    ) {
        $habilidad->update([
            'id_tipo_habilidad' => $request->id_tipo_habilidad,
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_mod' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.habilidad.index')
            ->with('success', 'Habilidad actualizada correctamente.');
    }

    public function destroy(Habilidad $habilidad)
    {
        $habilidad->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $habilidad->delete();

        return redirect()
            ->route('fac.catalogos.habilidad.index')
            ->with('success', 'Habilidad eliminada correctamente.');
    }
}