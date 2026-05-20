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

        $municipios = Municipio::with(['pais', 'departamento', 'municipioMh'])
            ->when($buscar, function ($query) use ($buscar) {
                $query->where('nombre_distrito', 'like', "%{$buscar}%")
                      ->orWhere('mh_codigo_distrito', 'like', "%{$buscar}%")
                      ->orWhereHas('departamento', function ($q) use ($buscar) {
                          $q->where('nombre_departamento', 'like', "%{$buscar}%");
                      })
                      ->orWhereHas('pais', function ($q) use ($buscar) {
                          $q->where('nombre_pais', 'like', "%{$buscar}%");
                      });
            })
            ->orderBy('nombre_distrito')
            ->paginate(10)
            ->withQueryString();

        return view('fac.catalogos.municipios.index', compact('municipios', 'buscar'));
    }

    public function create()
    {
        $paises        = Pais::where('activo', true)->orderBy('nombre_pais')->get();
        $departamentos = collect();
        $municipiosMh  = collect();
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
            ->with('success', 'Municipio creado correctamente.');
    }

    public function edit($id)
    {
        $municipio     = Municipio::findOrFail($id);
        $paises        = Pais::where('activo', true)->orderBy('nombre_pais')->get();
        $departamentos = Departamento::where('activo', true)
                            ->where('id_pais', $municipio->id_pais)
                            ->orderBy('nombre_departamento')
                            ->get();
        $municipiosMh  = MunicipioMh::where('activo', true)
                            ->where('id_departamento', $municipio->id_departamento)
                            ->orderBy('municipio_mh_nombre')
                            ->get();

        return view('fac.catalogos.municipios.edit', compact('municipio', 'paises', 'departamentos', 'municipiosMh'));
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
            ->with('success', 'Municipio actualizado correctamente.');
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
            ->with('success', 'Municipio eliminado correctamente.');
    }

    // ── Endpoints AJAX para los combos en cascada ──────────────────────────

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