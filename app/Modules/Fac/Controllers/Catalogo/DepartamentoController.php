<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Departamento;
use App\Modules\Fac\Models\Pais;
use App\Modules\Fac\Requests\StoreDepartamentoRequest;
use App\Modules\Fac\Requests\UpdateDepartamentoRequest;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $departamentos = Departamento::with('pais')
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre_departamento', 'like', "%{$buscar}%")
                      ->orWhere('mh_codigo_depto', 'like', "%{$buscar}%")
                      ->orWhereHas('pais', function ($q) use ($buscar) {
                          $q->where('nombre_pais', 'like', "%{$buscar}%");
                      });
            })
            ->orderBy('nombre_departamento')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.departamentos.index', compact('departamentos', 'buscar'));
    }

    public function create()
    {
        $paises = Pais::where('activo', true)->orderBy('nombre_pais')->get();
        return view('fac.catalogos.departamentos.create', compact('paises'));
    }

    public function store(StoreDepartamentoRequest $request)
    {
        Departamento::create([
            'id_pais'              => $request->id_pais,
            'nombre_departamento'  => $request->nombre_departamento,
            'mh_codigo_depto'      => $request->mh_codigo_depto,
            'georeferencia'        => $request->georeferencia,
            'activo'               => $request->boolean('activo'),
            'usuario_crea'         => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.departamentos.index')
            ->with('success', 'Departamento creado correctamente.');
    }

    public function edit($id)
    {
        $departamento = Departamento::findOrFail($id);
        $paises = Pais::where('activo', true)->orderBy('nombre_pais')->get();
        return view('fac.catalogos.departamentos.edit', compact('departamento', 'paises'));
    }

    public function update(UpdateDepartamentoRequest $request, $id)
    {
        $departamento = Departamento::findOrFail($id);

        $departamento->update([
            'id_pais'              => $request->id_pais,
            'nombre_departamento'  => $request->nombre_departamento,
            'mh_codigo_depto'      => $request->mh_codigo_depto,
            'georeferencia'        => $request->georeferencia,
            'activo'               => $request->boolean('activo'),
            'usuario_mod'          => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.departamentos.index')
            ->with('success', 'Departamento actualizado correctamente.');
    }

    public function destroy($id)
    {
        $departamento = Departamento::findOrFail($id);

        $departamento->update([
            'activo'       => false,
            'usuario_elim' => auth()->id(),
        ]);
        $departamento->delete();

        return redirect()
            ->route('fac.catalogos.departamentos.index')
            ->with('success', 'Departamento eliminado correctamente.');
    }
}