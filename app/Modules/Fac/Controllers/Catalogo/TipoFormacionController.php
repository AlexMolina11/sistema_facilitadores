<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\TipoFormacion;
use App\Modules\Fac\Requests\StoreTipoFormacionRequest;
use App\Modules\Fac\Requests\UpdateTipoFormacionRequest;
use Illuminate\Http\Request;

class TipoFormacionController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $tiposFormacion = TipoFormacion::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view(
            'fac.catalogos.tipo-formacion.index',
            compact('tiposFormacion', 'buscar')
        );
    }

    public function create()
    {
        return view('fac.catalogos.tipo-formacion.create');
    }

    public function store(StoreTipoFormacionRequest $request)
    {
        TipoFormacion::create([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_crea' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.tipo-formacion.index')
            ->with('success', 'Tipo de formación creado correctamente.');
    }

    public function edit(TipoFormacion $tipoFormacion)
    {
        return view(
            'fac.catalogos.tipo-formacion.edit',
            compact('tipoFormacion')
        );
    }

    public function update(
        UpdateTipoFormacionRequest $request,
        TipoFormacion $tipoFormacion
    ) {
        $tipoFormacion->update([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_mod' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.tipo-formacion.index')
            ->with('success', 'Tipo de formación actualizado correctamente.');
    }

    public function destroy(TipoFormacion $tipoFormacion)
    {
        $tipoFormacion->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $tipoFormacion->delete();

        return redirect()
            ->route('fac.catalogos.tipo-formacion.index')
            ->with('success', 'Tipo de formación eliminado correctamente.');
    }
}