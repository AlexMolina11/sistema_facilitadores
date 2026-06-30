<?php

namespace App\Modules\Fac\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Departamento;
use App\Modules\Fac\Models\Municipio;
use App\Modules\Fac\Models\MunicipioMh;
use App\Modules\Fac\Models\Pais;
use App\Modules\Fac\Requests\StoreMunicipioRequest;
use App\Modules\Fac\Requests\UpdateMunicipioRequest;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');
        $idPais = $request->get('id_pais');
        $idDepartamento = $request->get('id_departamento');
        $idMunicipioMh = $request->get('id_municipio_mh');

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

        $municipiosMh = MunicipioMh::with('departamento')
            ->where('activo', true)
            ->when($idDepartamento, function ($query) use ($idDepartamento) {
                $query->where('id_departamento', $idDepartamento);
            })
            ->orderBy('municipio_mh_nombre')
            ->get();

        $municipios = Municipio::with(['pais', 'departamento', 'municipioMh'])
            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombre_distrito', 'like', "%{$buscar}%")
                        ->orWhere('mh_codigo_distrito', 'like', "%{$buscar}%")
                        ->orWhereHas('departamento', function ($depto) use ($buscar) {
                            $depto->where('nombre_departamento', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('pais', function ($pais) use ($buscar) {
                            $pais->where('nombre_pais', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('municipioMh', function ($mh) use ($buscar) {
                            $mh->where('municipio_mh_nombre', 'like', "%{$buscar}%");
                        });
                });
            })
            ->when($idPais, function ($query) use ($idPais) {
                $query->where('id_pais', $idPais);
            })
            ->when($idDepartamento, function ($query) use ($idDepartamento) {
                $query->where('id_departamento', $idDepartamento);
            })
            ->when($idMunicipioMh, function ($query) use ($idMunicipioMh) {
                $query->where('id_municipio_mh', $idMunicipioMh);
            })
            ->orderBy('nombre_distrito')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.municipios.index', compact(
            'municipios',
            'buscar',
            'paises',
            'departamentos',
            'municipiosMh',
            'idPais',
            'idDepartamento',
            'idMunicipioMh'
        ));
    }

    public function create()
    {
        $paises = Pais::where('activo', true)
            ->orderBy('nombre_pais')
            ->get();

        $departamentos = collect();
        $municipiosMh = collect();

        return view('fac.catalogos.municipios.create', compact('paises', 'departamentos', 'municipiosMh'));
    }

    public function store(StoreMunicipioRequest $request)
    {
        Municipio::create([
            'id_pais'            => $request->id_pais,
            'id_departamento'    => $request->id_departamento,
            'id_municipio_mh'    => $request->id_municipio_mh,
            'nombre_distrito'    => $request->nombre_distrito,
            'mh_codigo_distrito' => $request->mh_codigo_distrito,
            'georeferencia'      => $request->georeferencia,
            'activo'             => $request->boolean('activo'),
            'usuario_crea'       => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.municipios.index')
            ->with('success', 'Distrito creado correctamente.');
    }

    public function edit($id)
    {
        $municipio = Municipio::findOrFail($id);

        $paises = Pais::where('activo', true)
            ->orderBy('nombre_pais')
            ->get();

        $departamentos = Departamento::where('activo', true)
            ->where('id_pais', $municipio->id_pais)
            ->orderBy('nombre_departamento')
            ->get();

        $municipiosMh = MunicipioMh::where('activo', true)
            ->where('id_departamento', $municipio->id_departamento)
            ->orderBy('municipio_mh_nombre')
            ->get();

        return view('fac.catalogos.municipios.edit', compact(
            'municipio',
            'paises',
            'departamentos',
            'municipiosMh'
        ));
    }

    public function update(UpdateMunicipioRequest $request, $id)
    {
        $municipio = Municipio::findOrFail($id);

        $municipio->update([
            'id_pais'            => $request->id_pais,
            'id_departamento'    => $request->id_departamento,
            'id_municipio_mh'    => $request->id_municipio_mh,
            'nombre_distrito'    => $request->nombre_distrito,
            'mh_codigo_distrito' => $request->mh_codigo_distrito,
            'georeferencia'      => $request->georeferencia,
            'activo'             => $request->boolean('activo'),
            'usuario_mod'        => auth()->id(),
        ]);

        return redirect()
            ->route('fac.catalogos.municipios.index')
            ->with('success', 'Distrito actualizado correctamente.');
    }

    public function destroy($id)
    {
        $municipio = Municipio::findOrFail($id);

        $municipio->update([
            'activo'       => false,
            'usuario_elim' => auth()->id(),
        ]);

        $municipio->delete();

        return redirect()
            ->route('fac.catalogos.municipios.index')
            ->with('success', 'Distrito eliminado correctamente.');
    }

    public function departamentosPorPais(Request $request)
    {
        $departamentos = Departamento::where('activo', true)
            ->where('id_pais', $request->id_pais)
            ->orderBy('nombre_departamento')
            ->get(['id_departamento', 'nombre_departamento']);

        return response()->json($departamentos);
    }

    public function municipiosMhPorDepartamento(Request $request)
    {
        $municipios = MunicipioMh::where('activo', true)
            ->where('id_departamento', $request->id_departamento)
            ->orderBy('municipio_mh_nombre')
            ->get(['id_municipio_mh', 'municipio_mh_nombre']);

        return response()->json($municipios);
    }
}