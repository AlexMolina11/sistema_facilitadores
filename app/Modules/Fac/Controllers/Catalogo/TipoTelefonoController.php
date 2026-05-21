<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\TipoTelefono;
use App\Modules\Fac\Requests\StoreTipoTelefonoRequest;
use App\Modules\Fac\Requests\UpdateTipoTelefonoRequest;
use Illuminate\Http\Request;

class TipoTelefonoController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $tipos = TipoTelefono::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.tipo_telefono.index', compact('tipos', 'buscar'));
    }

    public function create()
    {
        return view('fac.catalogos.tipo_telefono.create');
    }

    public function store(StoreTipoTelefonoRequest $request)
    {
        TipoTelefono::create([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
        ]);

        return redirect()
            ->route('fac.catalogos.tipo_telefono.index')
            ->with('success', 'Tipo de teléfono creado correctamente.');
    }

    public function edit($id)
    {
        $tipo = TipoTelefono::findOrFail($id);
        return view('fac.catalogos.tipo_telefono.edit', compact('tipo'));
    }

    public function update(UpdateTipoTelefonoRequest $request, $id)
    {
        $tipo = TipoTelefono::findOrFail($id);

        $tipo->update([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
        ]);

        return redirect()
            ->route('fac.catalogos.tipo_telefono.index')
            ->with('success', 'Tipo de teléfono actualizado correctamente.');
    }

    public function destroy($id)
    {
        $tipo = TipoTelefono::findOrFail($id);

        $tipo->update(['activo' => false]);
        $tipo->delete();

        return redirect()
            ->route('fac.catalogos.tipo_telefono.index')
            ->with('success', 'Tipo de teléfono eliminado correctamente.');
    }
}