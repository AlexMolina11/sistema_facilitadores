<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Departamento;
use App\Modules\Fac\Models\MunicipioMh;
use App\Modules\Fac\Models\Pais;
use App\Modules\Fac\Requests\StoreMunicipioMhRequest;
use App\Modules\Fac\Requests\UpdateMunicipioMhRequest;
use Illuminate\Http\Request;

class MunicipioMhController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');
        $idPais = $request->get('id_pais');
        $idDepartamento = $request->get('id_departamento');

        $paises = Pais::where('activo', true)
            ->orderBy('nombre_pais')
            ->get();

        $departamentos = Departamento::with('pais')
            ->where('activo', true)
            ->when($idPais, function ($query) use ($idPais) {
                $query->where('id_pais', $idPais);
            })
            ->orderBy('nombre_departamento')
            ->get();

        $municipios = MunicipioMh::with(['departamento.pais'])
            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('municipio_mh_nombre', 'like', "%{$buscar}%")
                        ->orWhere('mh_codigo_municipio', 'like', "%{$buscar}%")
                        ->orWhereHas('departamento', function ($depto) use ($buscar) {
                            $depto->where('nombre_departamento', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('departamento.pais', function ($pais) use ($buscar) {
                            $pais->where('nombre_pais', 'like', "%{$buscar}%");
                        });
                });
            })
            ->when($idPais, function ($query) use ($idPais) {
                $query->whereHas('departamento', function ($depto) use ($idPais) {
                    $depto->where('id_pais', $idPais);
                });
            })
            ->when($idDepartamento, function ($query) use ($idDepartamento) {
                $query->where('id_departamento', $idDepartamento);
            })
            ->orderBy('municipio_mh_nombre')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.municipios_mh.index', compact(
            'municipios',
            'buscar',
            'paises',
            'departamentos',
            'idPais',
            'idDepartamento'
        ));
    }

    public function create()
    {
        $departamentos = Departamento::with('pais')
            ->where('activo', true)
            ->orderBy('nombre_departamento')
            ->get();

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
        $municipio = MunicipioMh::findOrFail($id);

        $departamentos = Departamento::with('pais')
            ->where('activo', true)
            ->orderBy('nombre_departamento')
            ->get();

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