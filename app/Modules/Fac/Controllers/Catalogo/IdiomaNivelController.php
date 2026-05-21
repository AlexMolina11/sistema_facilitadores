<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\IdiomaNivel;
use App\Modules\Fac\Requests\StoreIdiomaNivelRequest;
use App\Modules\Fac\Requests\UpdateIdiomaNivelRequest;
use Illuminate\Http\Request;

class IdiomaNivelController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $niveles = IdiomaNivel::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.idioma-nivel.index', compact('niveles', 'buscar'));
    }

    public function create()
    {
        return view('fac.catalogos.idioma-nivel.create');
    }

    public function store(StoreIdiomaNivelRequest $request)
    {
        IdiomaNivel::create([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_crea' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.idioma-nivel.index')
            ->with('success', 'Nivel de idioma creado correctamente.');
    }

    public function edit(IdiomaNivel $idioma_nivel)
    {
        return view('fac.catalogos.idioma-nivel.edit', compact('idioma_nivel'));
    }

    public function update(UpdateIdiomaNivelRequest $request, IdiomaNivel $idioma_nivel)
    {
        $idioma_nivel->update([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_mod' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.idioma-nivel.index')
            ->with('success', 'Nivel de idioma actualizado correctamente.');
    }

    public function destroy(IdiomaNivel $idioma_nivel)
    {
        $idioma_nivel->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $idioma_nivel->delete();

        return redirect()
            ->route('fac.catalogos.idioma-nivel.index')
            ->with('success', 'Nivel de idioma eliminado correctamente.');
    }
}