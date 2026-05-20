<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\TipoConsultoria;
use App\Modules\Fac\Requests\StoreTipoConsultoriaRequest;
use App\Modules\Fac\Requests\UpdateTipoConsultoriaRequest;
use Illuminate\Http\Request;

class TipoConsultoriaController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $tipos = TipoConsultoria::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.tipo-consultoria.index', compact('tipos', 'buscar'));
    }

    public function create()
    {
        return view('fac.catalogos.tipo-consultoria.create');
    }

    public function store(StoreTipoConsultoriaRequest $request)
    {
        TipoConsultoria::create([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_crea' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.tipo-consultoria.index')
            ->with('success', 'Tipo de consultoría creado correctamente.');
    }

    public function edit(TipoConsultoria $tipo_consultoria)
    {
        return view('fac.catalogos.tipo-consultoria.edit', compact('tipo_consultoria'));
    }

    public function update(UpdateTipoConsultoriaRequest $request, TipoConsultoria $tipo_consultoria)
    {
        $tipo_consultoria->update([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_mod' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.tipo-consultoria.index')
            ->with('success', 'Tipo de consultoría actualizado correctamente.');
    }

    public function destroy(TipoConsultoria $tipo_consultoria)
    {
        $tipo_consultoria->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $tipo_consultoria->delete();

        return redirect()
            ->route('fac.catalogos.tipo-consultoria.index')
            ->with('success', 'Tipo de consultoría eliminado correctamente.');
    }
}