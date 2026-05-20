<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\TipoAtestado;
use App\Modules\Fac\Models\TipoFormacion;
use Illuminate\Http\Request;

use App\Modules\Fac\Requests\StoreTipoAtestadoRequest;
use App\Modules\Fac\Requests\UpdateTipoAtestadoRequest;

class TipoAtestadoController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $tiposAtestado = TipoAtestado::with('tipoFormacion')
            ->when($buscar, function ($query) use ($buscar) {

                $query->where('nombre', 'like', "%{$buscar}%")
                    ->orWhereHas('tipoFormacion', function ($q) use ($buscar) {
                        $q->where('nombre', 'like', "%{$buscar}%");
                    });

            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view(
            'fac.catalogos.tipo-atestado.index',
            compact('tiposAtestado', 'buscar')
        );
    }

    public function create()
    {
        $tiposFormacion = TipoFormacion::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'fac.catalogos.tipo-atestado.create',
            compact('tiposFormacion')
        );
    }

    public function store(StoreTipoAtestadoRequest $request)
    {
        TipoAtestado::create([
            'id_tipo_formacion' => $request->id_tipo_formacion,
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_crea' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.tipo-atestado.index')
            ->with('success', 'Tipo de atestado creado correctamente.');
    }

    public function edit(TipoAtestado $tipoAtestado)
    {
        $tiposFormacion = TipoFormacion::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'fac.catalogos.tipo-atestado.edit',
            compact('tipoAtestado', 'tiposFormacion')
        );
    }

    public function update(
        UpdateTipoAtestadoRequest $request,
        TipoAtestado $tipoAtestado
    ) {
        $tipoAtestado->update([
            'id_tipo_formacion' => $request->id_tipo_formacion,
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_mod' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.tipo-atestado.index')
            ->with('success', 'Tipo de atestado actualizado correctamente.');
    }

    public function destroy(TipoAtestado $tipoAtestado)
    {
        $tipoAtestado->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $tipoAtestado->delete();

        return redirect()
            ->route('fac.catalogos.tipo-atestado.index')
            ->with('success', 'Tipo de atestado eliminado correctamente.');
    }
}