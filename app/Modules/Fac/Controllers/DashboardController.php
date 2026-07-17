<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $this->resolverFiltros($request);
        $catalogos = $this->catalogosFiltros();

        $base = $this->consultoresBase($filtros);

        $totalConsultores = (clone $base)->count();
        $consultoresActivos = (clone $base)->where('c.activo', true)->count();
        $consultoresInactivos = (clone $base)->where('c.activo', false)->count();
        $consultoresVigentes = (clone $base)->where('c.vigente', true)->count();

        $conExperiencia = $this->countDistinctFromRelation('tbl_consultor_experiencia_laboral as el', 'el.id_consultor', $filtros, function ($q) {
            $q->whereNull('el.deleted_at')->where('el.activo', true);
        });

        $conAtestados = $this->countDistinctFromRelation('tbl_consultor_atestado as ca', 'ca.id_consultor', $filtros, function ($q) {
            $q->whereNull('ca.deleted_at')->where('ca.activo', true);
        });

        $conAreas = $this->countDistinctFromRelation('tbl_consultor_area_especializacion as cae', 'cae.id_consultor', $filtros, function ($q) {
            $q->whereNull('cae.deleted_at')->where('cae.activo', true);
        });

        $conIdiomas = $this->countDistinctFromRelation('tbl_consultor_idioma as ci', 'ci.id_consultor', $filtros, function ($q) {
            $q->whereNull('ci.deleted_at')->where('ci.activo', true);
        });

        $porcentajeActivos = $this->porcentaje($consultoresActivos, $totalConsultores);
        $porcentajeConExperiencia = $this->porcentaje($conExperiencia, $totalConsultores);
        $porcentajeConAtestados = $this->porcentaje($conAtestados, $totalConsultores);
        $porcentajeConAreas = $this->porcentaje($conAreas, $totalConsultores);
        $porcentajeConIdiomas = $this->porcentaje($conIdiomas, $totalConsultores);

        $idiomasUnicos = $this->queryRelacionFiltrada('tbl_consultor_idioma as ci', 'ci.id_consultor', $filtros)
            ->whereNull('ci.deleted_at')
            ->where('ci.activo', true)
            ->distinct('ci.id_idioma')
            ->count('ci.id_idioma');

        $experienciaPromedio = $this->calcularExperienciaPromedio($filtros);

        $invitacionesActivas = $this->contarInvitaciones('activas');
        $invitacionesVencidas = $this->contarInvitaciones('vencidas');
        $invitacionesUsadas = $this->contarInvitaciones('usadas');

        $distribucionPais = (clone $base)
            ->leftJoin('tbl_pais as p', 'p.id_pais', '=', 'c.id_pais')
            ->selectRaw('COALESCE(p.nombre_pais, "Sin país") as nombre, COUNT(DISTINCT c.id_consultor) as total')
            ->groupBy('nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $distribucionSexo = (clone $base)
            ->leftJoin('tbl_sexo as s', 's.id_sexo', '=', 'c.id_sexo')
            ->selectRaw('COALESCE(s.nombre, "Sin registro") as nombre, COUNT(DISTINCT c.id_consultor) as total')
            ->groupBy('nombre')
            ->orderByDesc('total')
            ->get();

        $topHabilidades = DB::table('tbl_consultor_area_habilidad as cah')
            ->join(
                'tbl_consultor_area_especializacion as cae',
                'cae.id_consultor_area',
                '=',
                'cah.id_consultor_area'
            )
            ->join(
                'tbl_consultor as c',
                'c.id_consultor',
                '=',
                'cae.id_consultor'
            )
            ->join(
                'tbl_habilidad_tecnica as ht',
                'ht.id_habilidad_tecnica',
                '=',
                'cah.id_habilidad_tecnica'
            )

            // Registros no eliminados
            ->whereNull('cah.deleted_at')
            ->whereNull('cae.deleted_at')
            ->whereNull('c.deleted_at')
            ->whereNull('ht.deleted_at')

            /*
            * Compatibilidad con registros antiguos:
            * activo = 1 o activo = NULL se consideran visibles.
            */
            ->where(function ($query) {
                $query->where('cah.activo', true)
                    ->orWhereNull('cah.activo');
            })
            ->where(function ($query) {
                $query->where('cae.activo', true)
                    ->orWhereNull('cae.activo');
            })
            ->where(function ($query) {
                $query->where('ht.activo', true)
                    ->orWhereNull('ht.activo');
            })

            // Filtro por periodo de registro del consultor
            ->when(
                $filtros['fecha_inicio'] && $filtros['fecha_fin'],
                function ($query) use ($filtros) {
                    $query->whereBetween('c.created_at', [
                        $filtros['fecha_inicio']->copy()->startOfDay(),
                        $filtros['fecha_fin']->copy()->endOfDay(),
                    ]);
                }
            )

            // Filtros generales
            ->when(
                $filtros['id_pais'],
                fn ($query) => $query->where(
                    'c.id_pais',
                    $filtros['id_pais']
                )
            )
            ->when(
                $filtros['id_sexo'],
                fn ($query) => $query->where(
                    'c.id_sexo',
                    $filtros['id_sexo']
                )
            )
            ->when(
                $filtros['estado'] === 'activos',
                fn ($query) => $query->where('c.activo', true)
            )
            ->when(
                $filtros['estado'] === 'inactivos',
                fn ($query) => $query->where('c.activo', false)
            )

            // Área seleccionada en los filtros
            ->when(
                $filtros['id_area_especializacion'],
                fn ($query) => $query->where(
                    'cae.id_area_especializacion',
                    $filtros['id_area_especializacion']
                )
            )

            // Disponibilidad seleccionada
            ->when(
                $filtros['id_tipo_disponibilidad'],
                function ($query) use ($filtros) {
                    $query->whereExists(function ($subquery) use ($filtros) {
                        $subquery
                            ->selectRaw('1')
                            ->from('tbl_consultor_disponibilidad as fcd')
                            ->whereColumn(
                                'fcd.id_consultor',
                                'c.id_consultor'
                            )
                            ->whereNull('fcd.deleted_at')
                            ->where(function ($query) {
                                $query->where('fcd.activo', true)
                                    ->orWhereNull('fcd.activo');
                            })
                            ->where(
                                'fcd.id_tipo_disponibilidad',
                                $filtros['id_tipo_disponibilidad']
                            );
                    });
                }
            )

            /*
            * Agrupamos por ID y nombre.
            * Esto evita mezclar habilidades distintas que casualmente
            * tengan el mismo nombre.
            */
            ->selectRaw('
                ht.id_habilidad_tecnica,
                ht.nombre AS nombre,
                COUNT(DISTINCT cae.id_consultor) AS total
            ')
            ->groupBy(
                'ht.id_habilidad_tecnica',
                'ht.nombre'
            )
            ->orderByDesc('total')
            ->orderBy('ht.nombre')
            ->limit(10)
            ->get();

        $topAreasEspecializacion = $this->queryRelacionFiltrada('tbl_consultor_area_especializacion as cae', 'cae.id_consultor', $filtros)
            ->join('tbl_area_especializacion as ae', 'ae.id_area_especializacion', '=', 'cae.id_area_especializacion')
            ->whereNull('cae.deleted_at')
            ->whereNull('ae.deleted_at')
            ->where('cae.activo', true)
            ->where('ae.activo', true)
            ->selectRaw('ae.nombre as nombre, COUNT(DISTINCT cae.id_consultor) as total')
            ->groupBy('ae.nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $idiomasFrecuentes = $this->queryRelacionFiltrada('tbl_consultor_idioma as ci', 'ci.id_consultor', $filtros)
            ->join('tbl_idioma as i', 'i.id_idioma', '=', 'ci.id_idioma')
            ->whereNull('ci.deleted_at')
            ->where('ci.activo', true)
            ->selectRaw('i.nombre as nombre, COUNT(DISTINCT ci.id_consultor) as total')
            ->groupBy('i.nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $disponibilidad = $this->queryRelacionFiltrada('tbl_consultor_disponibilidad as cd', 'cd.id_consultor', $filtros)
            ->join('tbl_tipo_disponibilidad as td', 'td.id_tipo_disponibilidad', '=', 'cd.id_tipo_disponibilidad')
            ->whereNull('cd.deleted_at')
            ->where('cd.activo', true)
            ->selectRaw('td.nombre as nombre, COUNT(DISTINCT cd.id_consultor) as total')
            ->groupBy('td.nombre')
            ->orderByDesc('total')
            ->get();

        $experienciaPorNivel = $this->queryRelacionFiltrada('tbl_consultor_atestado as ca', 'ca.id_consultor', $filtros)
            ->join('tbl_nivel_academico as na', 'na.id_nivel_academico', '=', 'ca.id_nivel_academico')
            ->whereNull('ca.deleted_at')
            ->where('ca.activo', true)
            ->whereNotNull('ca.id_nivel_academico')
            ->selectRaw('na.nombre as nombre, COUNT(DISTINCT ca.id_consultor) as total')
            ->groupBy('na.nombre')
            ->orderByDesc('total')
            ->get();

        $registrosPorMes = (clone $base)
            ->selectRaw("DATE_FORMAT(c.created_at, '%Y-%m') as periodo, COUNT(DISTINCT c.id_consultor) as total")
            ->whereNotNull('c.created_at')
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->limit(12)
            ->get()
            ->map(fn ($item) => [
                'nombre' => Carbon::createFromFormat('Y-m', $item->periodo)->translatedFormat('M Y'),
                'total' => (int) $item->total,
            ]);

        $ultimosConsultores = (clone $base)
            ->leftJoin('tbl_pais as p', 'p.id_pais', '=', 'c.id_pais')
            ->select('c.id_consultor', 'c.nombres', 'c.apellidos', 'c.activo', 'c.created_at', 'p.nombre_pais')
            ->orderByDesc('c.created_at')
            ->limit(8)
            ->get();

        $resumenCobertura = collect([
            ['nombre' => 'Con experiencia laboral', 'total' => $conExperiencia, 'porcentaje' => $porcentajeConExperiencia],
            ['nombre' => 'Con atestados', 'total' => $conAtestados, 'porcentaje' => $porcentajeConAtestados],
            ['nombre' => 'Con áreas de especialización', 'total' => $conAreas, 'porcentaje' => $porcentajeConAreas],
            ['nombre' => 'Con idiomas', 'total' => $conIdiomas, 'porcentaje' => $porcentajeConIdiomas],
        ]);

        return view('fac.dashboard', compact(
            'filtros',
            'catalogos',
            'totalConsultores',
            'consultoresActivos',
            'consultoresInactivos',
            'consultoresVigentes',
            'porcentajeActivos',
            'conExperiencia',
            'conAtestados',
            'conAreas',
            'conIdiomas',
            'porcentajeConExperiencia',
            'porcentajeConAtestados',
            'porcentajeConAreas',
            'porcentajeConIdiomas',
            'idiomasUnicos',
            'experienciaPromedio',
            'invitacionesActivas',
            'invitacionesVencidas',
            'invitacionesUsadas',
            'distribucionPais',
            'distribucionSexo',
            'topHabilidades',
            'topAreasEspecializacion',
            'idiomasFrecuentes',
            'disponibilidad',
            'experienciaPorNivel',
            'registrosPorMes',
            'ultimosConsultores',
            'resumenCobertura'
        ));
    }

    public function exportarCsv(Request $request): StreamedResponse
    {
        $filtros = $this->resolverFiltros($request);
        $fileName = 'dashboard_facilitadores_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($filtros) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Nombres', 'Apellidos', 'País', 'Sexo', 'Estado', 'Vigente', 'Fecha registro']);

            $this->consultoresBase($filtros)
                ->leftJoin('tbl_pais as p', 'p.id_pais', '=', 'c.id_pais')
                ->leftJoin('tbl_sexo as s', 's.id_sexo', '=', 'c.id_sexo')
                ->select('c.id_consultor', 'c.nombres', 'c.apellidos', 'p.nombre_pais', 's.nombre as sexo', 'c.activo', 'c.vigente', 'c.created_at')
                ->orderBy('c.apellidos')
                ->orderBy('c.nombres')
                ->chunk(500, function ($items) use ($handle) {
                    foreach ($items as $item) {
                        fputcsv($handle, [
                            $item->id_consultor,
                            $item->nombres,
                            $item->apellidos,
                            $item->nombre_pais ?? 'Sin país',
                            $item->sexo ?? 'Sin registro',
                            $item->activo ? 'Activo' : 'Inactivo',
                            $item->vigente ? 'Sí' : 'No',
                            optional($item->created_at ? Carbon::parse($item->created_at) : null)->format('Y-m-d'),
                        ]);
                    }
                });

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function resolverFiltros(Request $request): array
    {
        $filtroFecha = $request->get('filtro_fecha', 'todos');

        [$fechaInicio, $fechaFin] = match ($filtroFecha) {
            'hoy' => [now(), now()],
            '7_dias' => [now()->subDays(6), now()],
            '30_dias' => [now()->subDays(29), now()],
            'este_mes' => [now()->startOfMonth(), now()->endOfMonth()],
            'mes_pasado' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
            'rango' => [
                $request->filled('fecha_inicio') ? Carbon::parse($request->fecha_inicio) : null,
                $request->filled('fecha_fin') ? Carbon::parse($request->fecha_fin) : null,
            ],
            default => [null, null],
        };

        return [
            'filtro_fecha' => $filtroFecha,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'id_pais' => $request->filled('id_pais') ? (int) $request->id_pais : null,
            'id_sexo' => $request->filled('id_sexo') ? (int) $request->id_sexo : null,
            'id_area_especializacion' => $request->filled('id_area_especializacion') ? (int) $request->id_area_especializacion : null,
            'id_tipo_disponibilidad' => $request->filled('id_tipo_disponibilidad') ? (int) $request->id_tipo_disponibilidad : null,
            'estado' => $request->get('estado', 'todos'),
        ];
    }

    private function catalogosFiltros(): array
    {
        return [
            'paises' => DB::table('tbl_pais')->whereNull('deleted_at')->where('activo', true)->orderBy('nombre_pais')->get(),
            'sexos' => DB::table('tbl_sexo')->whereNull('deleted_at')->where('activo', true)->orderBy('nombre')->get(),
            'areas' => DB::table('tbl_area_especializacion')->whereNull('deleted_at')->where('activo', true)->orderBy('nombre')->get(),
            'disponibilidades' => DB::table('tbl_tipo_disponibilidad')->whereNull('deleted_at')->where('activo', true)->orderBy('nombre')->get(),
        ];
    }

    private function consultoresBase(array $filtros)
    {
        return DB::table('tbl_consultor as c')
            ->whereNull('c.deleted_at')
            ->when($filtros['fecha_inicio'] && $filtros['fecha_fin'], function ($query) use ($filtros) {
                $query->whereBetween('c.created_at', [
                    $filtros['fecha_inicio']->copy()->startOfDay(),
                    $filtros['fecha_fin']->copy()->endOfDay(),
                ]);
            })
            ->when($filtros['id_pais'], fn ($query) => $query->where('c.id_pais', $filtros['id_pais']))
            ->when($filtros['id_sexo'], fn ($query) => $query->where('c.id_sexo', $filtros['id_sexo']))
            ->when($filtros['estado'] === 'activos', fn ($query) => $query->where('c.activo', true))
            ->when($filtros['estado'] === 'inactivos', fn ($query) => $query->where('c.activo', false))
            ->when($filtros['id_area_especializacion'], function ($query) use ($filtros) {
                $query->whereExists(function ($sub) use ($filtros) {
                    $sub->selectRaw('1')
                        ->from('tbl_consultor_area_especializacion as fcae')
                        ->whereColumn('fcae.id_consultor', 'c.id_consultor')
                        ->whereNull('fcae.deleted_at')
                        ->where('fcae.activo', true)
                        ->where('fcae.id_area_especializacion', $filtros['id_area_especializacion']);
                });
            })
            ->when($filtros['id_tipo_disponibilidad'], function ($query) use ($filtros) {
                $query->whereExists(function ($sub) use ($filtros) {
                    $sub->selectRaw('1')
                        ->from('tbl_consultor_disponibilidad as fcd')
                        ->whereColumn('fcd.id_consultor', 'c.id_consultor')
                        ->whereNull('fcd.deleted_at')
                        ->where('fcd.activo', true)
                        ->where('fcd.id_tipo_disponibilidad', $filtros['id_tipo_disponibilidad']);
                });
            });
    }

    private function queryRelacionFiltrada(string $tabla, string $columnaConsultor, array $filtros)
    {
        return DB::table($tabla)
            ->join('tbl_consultor as c', 'c.id_consultor', '=', DB::raw($columnaConsultor))
            ->whereNull('c.deleted_at')
            ->when($filtros['fecha_inicio'] && $filtros['fecha_fin'], function ($query) use ($filtros) {
                $query->whereBetween('c.created_at', [
                    $filtros['fecha_inicio']->copy()->startOfDay(),
                    $filtros['fecha_fin']->copy()->endOfDay(),
                ]);
            })
            ->when($filtros['id_pais'], fn ($query) => $query->where('c.id_pais', $filtros['id_pais']))
            ->when($filtros['id_sexo'], fn ($query) => $query->where('c.id_sexo', $filtros['id_sexo']))
            ->when($filtros['estado'] === 'activos', fn ($query) => $query->where('c.activo', true))
            ->when($filtros['estado'] === 'inactivos', fn ($query) => $query->where('c.activo', false))
            ->when($filtros['id_area_especializacion'], function ($query) use ($filtros) {
                $query->whereExists(function ($sub) use ($filtros) {
                    $sub->selectRaw('1')
                        ->from('tbl_consultor_area_especializacion as fcae')
                        ->whereColumn('fcae.id_consultor', 'c.id_consultor')
                        ->whereNull('fcae.deleted_at')
                        ->where('fcae.activo', true)
                        ->where('fcae.id_area_especializacion', $filtros['id_area_especializacion']);
                });
            })
            ->when($filtros['id_tipo_disponibilidad'], function ($query) use ($filtros) {
                $query->whereExists(function ($sub) use ($filtros) {
                    $sub->selectRaw('1')
                        ->from('tbl_consultor_disponibilidad as fcd')
                        ->whereColumn('fcd.id_consultor', 'c.id_consultor')
                        ->whereNull('fcd.deleted_at')
                        ->where('fcd.activo', true)
                        ->where('fcd.id_tipo_disponibilidad', $filtros['id_tipo_disponibilidad']);
                });
            });
    }

    private function countDistinctFromRelation(string $tabla, string $columnaConsultor, array $filtros, callable $scope): int
    {
        $query = $this->queryRelacionFiltrada($tabla, $columnaConsultor, $filtros);
        $scope($query);
        return $query->distinct($columnaConsultor)->count($columnaConsultor);
    }

    private function porcentaje(int $valor, int $total): int
    {
        return $total > 0 ? (int) round(($valor / $total) * 100) : 0;
    }

    private function contarInvitaciones(string $tipo): int
    {
        if (!DB::getSchemaBuilder()->hasTable('seg_invitaciones')) {
            return 0;
        }

        $query = DB::table('seg_invitaciones')->whereNull('deleted_at');

        return match ($tipo) {
            'activas' => $query->where('activa', true)->where('revocada', false)->where(function ($q) {
                $q->whereNull('fecha_expiracion')->orWhere('fecha_expiracion', '>=', now());
            })->count(),
            'vencidas' => $query->whereNotNull('fecha_expiracion')->where('fecha_expiracion', '<', now())->count(),
            'usadas' => $query->where('usos_actuales', '>', 0)->count(),
            default => 0,
        };
    }

    private function calcularExperienciaPromedio(array $filtros): float
    {
        $experiencias = $this->queryRelacionFiltrada('tbl_consultor_experiencia_laboral as el', 'el.id_consultor', $filtros)
            ->whereNull('el.deleted_at')
            ->where('el.activo', true)
            ->whereNotNull('el.desde')
            ->get(['el.id_consultor', 'el.desde', 'el.hasta', 'el.trabajo_actual']);

        if ($experiencias->isEmpty()) {
            return 0;
        }

        $experienciaPorConsultor = $experiencias->groupBy('id_consultor')->map(function ($items) {
            return $items->sum(function ($experiencia) {
                $desde = Carbon::parse($experiencia->desde);
                $hasta = $experiencia->trabajo_actual ? now() : ($experiencia->hasta ? Carbon::parse($experiencia->hasta) : now());
                return max(0, $desde->floatDiffInYears($hasta));
            });
        });

        return round($experienciaPorConsultor->avg(), 1);
    }
}
