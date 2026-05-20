<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Pais;
use App\Modules\Fac\Requests\StorePaisRequest;
use App\Modules\Fac\Requests\UpdatePaisRequest;
use Illuminate\Http\Request;

class PaisController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $paises = Pais::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre_pais', 'like', "%{$buscar}%")
                      ->orWhere('codigo_pais', 'like', "%{$buscar}%");
            })
            ->orderBy('nombre_pais')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.paises.index', compact('paises', 'buscar'));
    }

    public function create()
    {
        return view('fac.catalogos.paises.create');
    }

    public function store(StorePaisRequest $request)
    {
        Pais::create([
            'codigo_pais'        => $request->codigo_pais,
            'nombre_pais'        => $request->nombre_pais,
            'mh_codigo_pais'     => $request->mh_codigo_pais,
            'mh_codigo_pais_new' => $request->mh_codigo_pais_new,
            'activo'             => $request->boolean('activo'),
            'usuario_crea'       => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.paises.index')
            ->with('success', 'País creado correctamente.');
    }

    public function edit($id)
    {
        $pais = Pais::findOrFail($id);
        return view('fac.catalogos.paises.edit', compact('pais'));
    }

    public function update(UpdatePaisRequest $request, $id)
    {
        $pais = Pais::findOrFail($id);

        $pais->update([
            'codigo_pais'        => $request->codigo_pais,
            'nombre_pais'        => $request->nombre_pais,
            'mh_codigo_pais'     => $request->mh_codigo_pais,
            'mh_codigo_pais_new' => $request->mh_codigo_pais_new,
            'activo'             => $request->boolean('activo'),
            'usuario_mod'        => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.paises.index')
            ->with('success', 'País actualizado correctamente.');
    }

    public function destroy($id)
    {
        $pais = Pais::findOrFail($id);

        $pais->update([
            'activo'       => false,
            'usuario_elim' => auth()->id(),
        ]);
        $pais->delete();

        return redirect()
            ->route('fac.catalogos.paises.index')
            ->with('success', 'País eliminado correctamente.');
    }
}