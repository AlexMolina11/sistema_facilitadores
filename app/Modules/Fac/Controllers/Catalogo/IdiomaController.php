<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Idioma;
use App\Modules\Fac\Requests\StoreIdiomaRequest;
use App\Modules\Fac\Requests\UpdateIdiomaRequest;
use Illuminate\Http\Request;

class IdiomaController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $idiomas = Idioma::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.idiomas.index', compact('idiomas', 'buscar'));
    }

    public function create()
    {
        return view('fac.catalogos.idiomas.create');
    }

    public function store(StoreIdiomaRequest $request)
    {
        Idioma::create([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_crea' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.idiomas.index')
            ->with('success', 'Idioma creado correctamente.');
    }

    public function edit(Idioma $idioma)
    {
        return view('fac.catalogos.idiomas.edit', compact('idioma'));
    }

    public function update(UpdateIdiomaRequest $request, Idioma $idioma)
    {
        $idioma->update([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_mod' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.idiomas.index')
            ->with('success', 'Idioma actualizado correctamente.');
    }

    public function destroy(Idioma $idioma)
    {
        $idioma->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $idioma->delete();

        return redirect()
            ->route('fac.catalogos.idiomas.index')
            ->with('success', 'Idioma eliminado correctamente.');
    }
}