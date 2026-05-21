<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Departamento;
use App\Modules\Fac\Models\MunicipioMh;
use App\Modules\Fac\Requests\StoreMunicipioMhRequest;
use App\Modules\Fac\Requests\UpdateMunicipioMhRequest;
use Illuminate\Http\Request;

class MunicipioMhController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $municipios = MunicipioMh::with('departamento')
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('municipio_mh_nombre', 'like', "%{$buscar}%")
                      ->orWhere('mh_codigo_municipio', 'like', "%{$buscar}%")
                      ->orWhereHas('departamento', function ($q) use ($buscar) {
                          $q->where('nombre_departamento', 'like', "%{$buscar}%");
                      });
            })
            ->orderBy('municipio_mh_nombre')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.municipios_mh.index', compact('municipios', 'buscar'));
    }

    public function create()
    {
        $departamentos = Departamento::where('activo', true)->orderBy('nombre_departamento')->get();
        return view('fac.catalogos.municipios_mh.create', compact('departamentos'));
    }

    public function store(StoreMunicipioMhRequest $request)
    {
        MunicipioMh::create([
            'id_departamento'     => $request->id_departamento,
            'municipio_mh_nombre' => $request->municipio_mh_nombre,
            'mh_codigo_municipio' => $request->mh_codigo_municipio,
            'activo'              => $request->boolean('activo'),
            'usuario_crea'        => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.municipios_mh.index')
            ->with('success', 'Municipio creado correctamente.');
    }

    public function edit($id)
    {
        $municipio     = MunicipioMh::findOrFail($id);
        $departamentos = Departamento::where('activo', true)->orderBy('nombre_departamento')->get();
        return view('fac.catalogos.municipios_mh.edit', compact('municipio', 'departamentos'));
    }

    public function update(UpdateMunicipioMhRequest $request, $id)
    {
        $municipio = MunicipioMh::findOrFail($id);

        $municipio->update([
            'id_departamento'     => $request->id_departamento,
            'municipio_mh_nombre' => $request->municipio_mh_nombre,
            'mh_codigo_municipio' => $request->mh_codigo_municipio,
            'activo'              => $request->boolean('activo'),
            'usuario_mod'         => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.municipios_mh.index')
            ->with('success', 'Municipio actualizado correctamente.');
    }

    public function destroy($id)
    {
        $municipio = MunicipioMh::findOrFail($id);

        $municipio->update([
            'activo'       => false,
            'usuario_elim' => auth()->id(),
        ]);
        $municipio->delete();

        return redirect()
            ->route('fac.catalogos.municipios_mh.index')
            ->with('success', 'Municipio eliminado correctamente.');
    }
}