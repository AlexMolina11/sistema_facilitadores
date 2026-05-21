<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\TipoDocumento;
use App\Modules\Fac\Requests\StoreTipoDocumentoRequest;
use App\Modules\Fac\Requests\UpdateTipoDocumentoRequest;
use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $tipos = TipoDocumento::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.tipo-documento.index', compact('tipos', 'buscar'));
    }

    public function create()
    {
        return view('fac.catalogos.tipo-documento.create');
    }

    public function store(StoreTipoDocumentoRequest $request)
    {
        TipoDocumento::create([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_crea' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.tipo-documento.index')
            ->with('success', 'Tipo de documento creado correctamente.');
    }

    public function edit(TipoDocumento $tipo_documento)
    {
        return view('fac.catalogos.tipo-documento.edit', compact('tipo_documento'));
    }

    public function update(UpdateTipoDocumentoRequest $request, TipoDocumento $tipo_documento)
    {
        $tipo_documento->update([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_mod' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.tipo-documento.index')
            ->with('success', 'Tipo de documento actualizado correctamente.');
    }

    public function destroy(TipoDocumento $tipo_documento)
    {
        $tipo_documento->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $tipo_documento->delete();

        return redirect()
            ->route('fac.catalogos.tipo-documento.index')
            ->with('success', 'Tipo de documento eliminado correctamente.');
    }
}