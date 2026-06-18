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
use Illuminate\Support\Facades\Crypt;

class BusquedaAvanzadaController extends Controller
{
    public function __construct(private readonly BusquedaConsultorService $busquedaService)
    {
    }

    public function index(BuscarConsultoresRequest $request)
    {
        $filtros = $this->resolverFiltros($request);

        $consultores = $this->busquedaService->buscar($filtros);
        $tokenFiltros = $this->crearTokenFiltros($filtros);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('fac.consultores.busqueda-avanzada.partials._resultados', [
                    'consultores' => $consultores,
                    'totalConsultores' => $consultores->total(),
                ])->render(),
                'total' => $consultores->total(),
                'url' => route('fac.busqueda.index', array_filter([
                    's' => $tokenFiltros,
                    'page' => $request->integer('page') > 1 ? $request->integer('page') : null,
                ])),
            ]);
        }

        return view('fac.consultores.busqueda-avanzada.index', [
            'consultores' => $consultores,
            'totalConsultores' => $consultores->total(),
            'filtros' => $filtros,
            'tokenFiltros' => $tokenFiltros,
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


    private function resolverFiltros(BuscarConsultoresRequest $request): array
    {
        if ($request->filled('s')) {
            try {
                $filtros = json_decode(Crypt::decryptString((string) $request->string('s')), true);
                return is_array($filtros) ? $this->limpiarFiltros($filtros) : [];
            } catch (\Throwable) {
                return [];
            }
        }

        return $this->limpiarFiltros($request->validated());
    }

    private function crearTokenFiltros(array $filtros): ?string
    {
        $filtros = $this->limpiarFiltros($filtros);

        if ($filtros === []) {
            return null;
        }

        return Crypt::encryptString(json_encode($filtros));
    }

    private function limpiarFiltros(array $filtros): array
    {
        unset($filtros['s'], $filtros['page']);

        return collect($filtros)
            ->filter(function ($value) {
                if (is_array($value)) {
                    return collect($value)->filter(fn ($item) => $item !== null && $item !== '')->isNotEmpty();
                }

                return $value !== null && $value !== '';
            })
            ->map(function ($value) {
                return is_array($value) ? array_values(array_filter($value, fn ($item) => $item !== null && $item !== '')) : $value;
            })
            ->all();
    }

    private function habilidadesPorTipo(int $tipo)
    {
        return Habilidad::where('activo', true)
            ->where('id_tipo_habilidad', $tipo)
            ->orderBy('nombre')
            ->get();
    }
}