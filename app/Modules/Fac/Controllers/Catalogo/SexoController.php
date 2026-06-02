<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Sexo;
use App\Modules\Fac\Requests\StoreSexoRequest;
use App\Modules\Fac\Requests\UpdateSexoRequest;
use Illuminate\Http\Request;

class SexoController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $sexos = Sexo::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.sexo.index', compact('sexos', 'buscar'));
    }

    public function create()
    {
        return view('fac.catalogos.sexo.create');
    }

    public function store(StoreSexoRequest $request)
    {
        Sexo::create([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_crea' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.sexo.index')
            ->with('success', 'Sexo creado correctamente.');
    }

    public function edit(Sexo $sexo)
    {
        return view('fac.catalogos.sexo.edit', compact('sexo'));
    }

    public function update(UpdateSexoRequest $request, Sexo $sexo)
    {
        $sexo->update([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_mod' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.sexo.index')
            ->with('success', 'Sexo actualizado correctamente.');
    }

    public function destroy(Sexo $sexo)
    {
        $sexo->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $sexo->delete();

        return redirect()
            ->route('fac.catalogos.sexo.index')
            ->with('success', 'Sexo eliminado correctamente.');
    }
}