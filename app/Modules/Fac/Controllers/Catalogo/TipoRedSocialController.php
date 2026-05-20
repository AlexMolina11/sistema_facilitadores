<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\TipoRedSocial;
use App\Modules\Fac\Requests\StoreTipoRedSocialRequest;
use App\Modules\Fac\Requests\UpdateTipoRedSocialRequest;
use Illuminate\Http\Request;

class TipoRedSocialController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $tiposRedSocial = TipoRedSocial::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('icono', 'like', "%{$buscar}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.tipo-red-social.index', compact('tiposRedSocial', 'buscar'));
    }

    public function create()
    {
        return view('fac.catalogos.tipo-red-social.create');
    }

    public function store(StoreTipoRedSocialRequest $request)
    {
        TipoRedSocial::create([
            'nombre' => $request->nombre,
            'icono' => $request->icono,
            'activo' => $request->boolean('activo'),
            'usuario_crea' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.tipo-red-social.index')
            ->with('success', 'Tipo de red social creado correctamente.');
    }

    public function edit(TipoRedSocial $tipoRedSocial)
    {
        return view('fac.catalogos.tipo-red-social.edit', compact('tipoRedSocial'));
    }

    public function update(UpdateTipoRedSocialRequest $request, TipoRedSocial $tipoRedSocial)
    {
        $tipoRedSocial->update([
            'nombre' => $request->nombre,
            'icono' => $request->icono,
            'activo' => $request->boolean('activo'),
            'usuario_mod' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.tipo-red-social.index')
            ->with('success', 'Tipo de red social actualizado correctamente.');
    }

    public function destroy(TipoRedSocial $tipoRedSocial)
    {
        $tipoRedSocial->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $tipoRedSocial->delete();

        return redirect()
            ->route('fac.catalogos.tipo-red-social.index')
            ->with('success', 'Tipo de red social eliminado correctamente.');
    }
}