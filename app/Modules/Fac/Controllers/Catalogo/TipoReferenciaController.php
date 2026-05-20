<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\TipoReferencia;
use App\Modules\Fac\Requests\StoreTipoReferenciaRequest;
use App\Modules\Fac\Requests\UpdateTipoReferenciaRequest;
use Illuminate\Http\Request;

class TipoReferenciaController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $tiposReferencia = TipoReferencia::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.tipo-referencia.index', compact('tiposReferencia', 'buscar'));
    }

    public function create()
    {
        return view('fac.catalogos.tipo-referencia.create');
    }

    public function store(StoreTipoReferenciaRequest $request)
    {
        TipoReferencia::create([
    'nombre' => $request->nombre,
    'activo' => $request->boolean('activo'),
    'usuario_crea' => auth()->id(),
]);

        return redirect()
            ->route('fac.catalogos.tipo-referencia.index')
            ->with('success', 'Tipo de referencia creado correctamente.');
    }

    public function edit(TipoReferencia $tipoReferencia)
    {
        return view('fac.catalogos.tipo-referencia.edit', compact('tipoReferencia'));
    }

    public function update(UpdateTipoReferenciaRequest $request, TipoReferencia $tipoReferencia)
    {
        $tipoReferencia->update([
    'nombre' => $request->nombre,
    'activo' => $request->boolean('activo'),
    'usuario_mod' => auth()->id(),
]);

        return redirect()
            ->route('fac.catalogos.tipo-referencia.index')
            ->with('success', 'Tipo de referencia actualizado correctamente.');
    }

    public function destroy(TipoReferencia $tipoReferencia)
    {
       $tipoReferencia->update([
    'activo' => false,
    'usuario_elim' => auth()->id(),
]);

$tipoReferencia->delete();

        return redirect()
            ->route('fac.catalogos.tipo-referencia.index')
            ->with('success', 'Tipo de referencia eliminado correctamente.');
    }
}