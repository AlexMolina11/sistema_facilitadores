<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Departamento;
use App\Modules\Fac\Models\Habilidad;
use App\Modules\Fac\Models\Idioma;
use App\Modules\Fac\Models\IdiomaNivel;
use App\Modules\Fac\Models\Municipio;
use App\Modules\Fac\Models\MunicipioMh;
use App\Modules\Fac\Models\NivelAcademico;
use App\Modules\Fac\Models\Pais;
use App\Modules\Fac\Models\Sexo;
use App\Modules\Fac\Models\TipoAtestado;
use App\Modules\Fac\Models\TipoDisponibilidad;
use App\Modules\Fac\Models\TipoFormacion;
use App\Modules\Fac\Requests\BuscarConsultoresRequest;
use App\Modules\Fac\Services\BusquedaConsultorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusquedaAvanzadaController extends Controller
{
    public function __construct(private readonly BusquedaConsultorService $busquedaService)
    {
    }

    public function index(BuscarConsultoresRequest $request)
    {
        $filtros = $request->validated();

        $consultores = $this->busquedaService->buscar($filtros);

        return view('fac.consultores.busqueda-avanzada.index', [
            'consultores' => $consultores,
            'totalConsultores' => $consultores->total(),
            'filtros' => $filtros,
            'areaEspecializacion' => $this->habilidadesPorTipo(1),
            'habilidadesBlandas' => $this->habilidadesPorTipo(2),
            'habilidadesTecnicas' => $this->habilidadesPorTipo(3),
            'sexos' => Sexo::where('activo', true)->orderBy('nombre')->get(),
            'paises' => Pais::where('activo', true)->orderBy('nombre_pais')->get(),
            'departamentos' => Departamento::where('activo', true)->orderBy('nombre_departamento')->get(),
            'municipiosMh' => MunicipioMh::where('activo', true)->orderBy('municipio_mh_nombre')->get(),
            'distritos' => Municipio::where('activo', true)->orderBy('nombre_distrito')->get(),
            'idiomas' => Idioma::where('activo', true)->orderBy('nombre')->get(),
            'nivelesIdioma' => IdiomaNivel::where('activo', true)->orderBy('nombre')->get(),
            'tiposFormacion' => TipoFormacion::where('activo', true)->orderBy('nombre')->get(),
            'nivelesAcademicos' => NivelAcademico::where('activo', true)->orderBy('nombre')->get(),
            'tiposAtestado' => TipoAtestado::where('activo', true)->orderBy('nombre')->get(),
            'tiposDisponibilidad' => TipoDisponibilidad::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function departamentosPorPais(Request $request): JsonResponse
    {
        $request->validate(['id_pais' => ['required', 'integer', 'exists:tbl_pais,id_pais']]);

        return response()->json(
            Departamento::where('id_pais', $request->integer('id_pais'))
                ->where('activo', true)
                ->orderBy('nombre_departamento')
                ->get(['id_departamento', 'nombre_departamento'])
        );
    }

    public function municipiosPorDepartamento(Request $request): JsonResponse
    {
        $request->validate(['id_departamento' => ['required', 'integer', 'exists:tbl_departamento,id_departamento']]);

        return response()->json(
            MunicipioMh::where('id_departamento', $request->integer('id_departamento'))
                ->where('activo', true)
                ->orderBy('municipio_mh_nombre')
                ->get(['id_municipio_mh', 'municipio_mh_nombre'])
        );
    }

    public function distritosPorMunicipio(Request $request): JsonResponse
    {
        $request->validate(['id_municipio_mh' => ['required', 'integer', 'exists:tbl_municipio_mh,id_municipio_mh']]);

        return response()->json(
            Municipio::where('id_municipio_mh', $request->integer('id_municipio_mh'))
                ->where('activo', true)
                ->orderBy('nombre_distrito')
                ->get(['id_municipio', 'nombre_distrito', 'id_pais', 'id_departamento', 'id_municipio_mh'])
        );
    }

    public function ubicacionPorDistrito(Request $request): JsonResponse
    {
        $request->validate(['id_municipio' => ['required', 'integer', 'exists:tbl_municipio,id_municipio']]);

        $distrito = Municipio::findOrFail($request->integer('id_municipio'));

        return response()->json([
            'pais' => $distrito->id_pais,
            'departamento' => $distrito->id_departamento,
            'municipio_mh' => $distrito->id_municipio_mh,
            'distrito' => $distrito->id_municipio,
        ]);
    }

    private function habilidadesPorTipo(int $tipo)
    {
        return Habilidad::where('activo', true)
            ->where('id_tipo_habilidad', $tipo)
            ->orderBy('nombre')
            ->get();
    }
}