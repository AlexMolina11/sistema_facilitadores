<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\NivelAcademico;
use App\Modules\Fac\Requests\StoreNivelAcademicoRequest;
use App\Modules\Fac\Requests\UpdateNivelAcademicoRequest;
use Illuminate\Http\Request;

class NivelAcademicoController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $niveles = NivelAcademico::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.nivel-academico.index', compact('niveles', 'buscar'));
    }

    public function create()
    {
        return view('fac.catalogos.nivel-academico.create');
    }

    public function store(StoreNivelAcademicoRequest $request)
    {
        NivelAcademico::create([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_crea' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.nivel-academico.index')
            ->with('success', 'Nivel académico creado correctamente.');
    }

    public function edit(NivelAcademico $nivel_academico)
    {
        return view('fac.catalogos.nivel-academico.edit', compact('nivel_academico'));
    }

    public function update(UpdateNivelAcademicoRequest $request, NivelAcademico $nivel_academico)
    {
        $nivel_academico->update([
            'nombre' => $request->nombre,
            'activo' => $request->boolean('activo'),
            'usuario_mod' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.nivel-academico.index')
            ->with('success', 'Nivel académico actualizado correctamente.');
    }

    public function destroy(NivelAcademico $nivel_academico)
    {
        $nivel_academico->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $nivel_academico->delete();

        return redirect()
            ->route('fac.catalogos.nivel-academico.index')
            ->with('success', 'Nivel académico eliminado correctamente.');
    }
}