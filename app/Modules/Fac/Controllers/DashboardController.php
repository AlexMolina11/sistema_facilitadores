<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        [$fechaInicio, $fechaFin, $filtroFecha] = $this->resolverFechas($request);

        $consultoresBase = DB::table('tbl_consultor')
            ->whereNull('deleted_at');

        if ($fechaInicio && $fechaFin) {
            $consultoresBase->whereBetween('created_at', [
                $fechaInicio->startOfDay(),
                $fechaFin->endOfDay(),
            ]);
        }

        $totalConsultores = (clone $consultoresBase)->count();

        $consultoresActivos = (clone $consultoresBase)
            ->where('activo', true)
            ->count();

        $consultoresInactivos = (clone $consultoresBase)
            ->where('activo', false)
            ->count();

        $idiomasUnicos = DB::table('tbl_consultor_idioma as ci')
            ->join('tbl_consultor as c', 'c.id_consultor', '=', 'ci.id_consultor')
            ->whereNull('ci.deleted_at')
            ->whereNull('c.deleted_at')
            ->where('ci.activo', true)
            ->distinct('ci.id_idioma')
            ->count('ci.id_idioma');

        $experienciaPromedio = $this->calcularExperienciaPromedio();

        $invitacionesActivas = $this->contarInvitaciones('activas');
        $invitacionesVencidas = $this->contarInvitaciones('vencidas');
        $invitacionesUsadas = $this->contarInvitaciones('usadas');

        $distribucionPais = DB::table('tbl_consultor as c')
            ->leftJoin('tbl_pais as p', 'p.id_pais', '=', 'c.id_pais')
            ->selectRaw('COALESCE(p.nombre_pais, "Sin país") as nombre, COUNT(*) as total')
            ->whereNull('c.deleted_at')
            ->when($fechaInicio && $fechaFin, function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('c.created_at', [
                    $fechaInicio->copy()->startOfDay(),
                    $fechaFin->copy()->endOfDay(),
                ]);
            })
            ->groupBy('nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $topHabilidades = DB::table('tbl_consultor_area_habilidad as cah')
            ->join('tbl_consultor_area_especializacion as cae', 'cae.id_consultor_area', '=', 'cah.id_consultor_area')
            ->join('tbl_consultor as c', 'c.id_consultor', '=', 'cae.id_consultor')
            ->join('tbl_habilidad_tecnica as ht', 'ht.id_habilidad_tecnica', '=', 'cah.id_habilidad_tecnica')
            ->whereNull('cah.deleted_at')
            ->whereNull('cae.deleted_at')
            ->whereNull('c.deleted_at')
            ->whereNull('ht.deleted_at')
            ->where('cah.activo', true)
            ->where('cae.activo', true)
            ->where('ht.activo', true)
            ->selectRaw('ht.nombre as nombre, COUNT(DISTINCT cae.id_consultor) as total')
            ->groupBy('ht.nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $topAreasEspecializacion = DB::table('tbl_consultor_area_especializacion as cae')
            ->join('tbl_consultor as c', 'c.id_consultor', '=', 'cae.id_consultor')
            ->join('tbl_area_especializacion as ae', 'ae.id_area_especializacion', '=', 'cae.id_area_especializacion')
            ->whereNull('cae.deleted_at')
            ->whereNull('c.deleted_at')
            ->whereNull('ae.deleted_at')
            ->where('cae.activo', true)
            ->where('ae.activo', true)
            ->selectRaw('ae.nombre as nombre, COUNT(DISTINCT cae.id_consultor) as total')
            ->groupBy('ae.nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $idiomasFrecuentes = DB::table('tbl_consultor_idioma as ci')
            ->join('tbl_consultor as c', 'c.id_consultor', '=', 'ci.id_consultor')
            ->join('tbl_idioma as i', 'i.id_idioma', '=', 'ci.id_idioma')
            ->whereNull('ci.deleted_at')
            ->whereNull('c.deleted_at')
            ->where('ci.activo', true)
            ->selectRaw('i.nombre as nombre, COUNT(DISTINCT ci.id_consultor) as total')
            ->groupBy('i.nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $disponibilidad = DB::table('tbl_consultor_disponibilidad as cd')
            ->join('tbl_consultor as c', 'c.id_consultor', '=', 'cd.id_consultor')
            ->join('tbl_tipo_disponibilidad as td', 'td.id_tipo_disponibilidad', '=', 'cd.id_tipo_disponibilidad')
            ->whereNull('cd.deleted_at')
            ->whereNull('c.deleted_at')
            ->where('cd.activo', true)
            ->selectRaw('td.nombre as nombre, COUNT(DISTINCT cd.id_consultor) as total')
            ->groupBy('td.nombre')
            ->orderByDesc('total')
            ->get();

        $experienciaPorNivel = DB::table('tbl_consultor_atestado as ca')
            ->join('tbl_consultor as c', 'c.id_consultor', '=', 'ca.id_consultor')
            ->join('tbl_nivel_academico as na', 'na.id_nivel_academico', '=', 'ca.id_nivel_academico')
            ->whereNull('ca.deleted_at')
            ->whereNull('c.deleted_at')
            ->where('ca.activo', true)
            ->whereNotNull('ca.id_nivel_academico')
            ->selectRaw('na.nombre as nombre, COUNT(DISTINCT ca.id_consultor) as total')
            ->groupBy('na.nombre')
            ->orderByDesc('total')
            ->get();

        return view('fac.dashboard', compact(
            'filtroFecha',
            'fechaInicio',
            'fechaFin',
            'totalConsultores',
            'consultoresActivos',
            'consultoresInactivos',
            'idiomasUnicos',
            'experienciaPromedio',
            'invitacionesActivas',
            'invitacionesVencidas',
            'invitacionesUsadas',
            'distribucionPais',
            'topHabilidades',
            'topAreasEspecializacion',
            'idiomasFrecuentes',
            'disponibilidad',
            'experienciaPorNivel'
        ));
    }

    private function resolverFechas(Request $request): array
    {
        $filtro = $request->get('filtro_fecha', 'todos');

        return match ($filtro) {
            'hoy' => [now(), now(), $filtro],
            '7_dias' => [now()->subDays(6), now(), $filtro],
            '30_dias' => [now()->subDays(29), now(), $filtro],
            'este_mes' => [now()->startOfMonth(), now()->endOfMonth(), $filtro],
            'mes_pasado' => [
                now()->subMonthNoOverflow()->startOfMonth(),
                now()->subMonthNoOverflow()->endOfMonth(),
                $filtro
            ],
            'rango' => [
                $request->filled('fecha_inicio') ? Carbon::parse($request->fecha_inicio) : null,
                $request->filled('fecha_fin') ? Carbon::parse($request->fecha_fin) : null,
                $filtro
            ],
            default => [null, null, 'todos'],
        };
    }

    private function contarInvitaciones(string $tipo): int
    {
        if (!DB::getSchemaBuilder()->hasTable('seg_invitaciones')) {
            return 0;
        }

        $query = DB::table('seg_invitaciones')
            ->whereNull('deleted_at');

        return match ($tipo) {
            'activas' => $query
                ->where('activa', true)
                ->where('revocada', false)
                ->where(function ($q) {
                    $q->whereNull('fecha_expiracion')
                        ->orWhere('fecha_expiracion', '>=', now());
                })
                ->count(),

            'vencidas' => $query
                ->whereNotNull('fecha_expiracion')
                ->where('fecha_expiracion', '<', now())
                ->count(),

            'usadas' => $query
                ->where('usos_actuales', '>', 0)
                ->count(),

            default => 0,
        };
    }

    private function calcularExperienciaPromedio(): float
    {
        $experiencias = DB::table('tbl_consultor_experiencia_laboral')
            ->whereNull('deleted_at')
            ->where('activo', true)
            ->whereNotNull('desde')
            ->get(['id_consultor', 'desde', 'hasta', 'trabajo_actual']);

        if ($experiencias->isEmpty()) {
            return 0;
        }

        $experienciaPorConsultor = $experiencias
            ->groupBy('id_consultor')
            ->map(function ($items) {
                return $items->sum(function ($experiencia) {
                    $desde = Carbon::parse($experiencia->desde);
                    $hasta = $experiencia->trabajo_actual
                        ? now()
                        : ($experiencia->hasta ? Carbon::parse($experiencia->hasta) : now());

                    return max(0, $desde->floatDiffInYears($hasta));
                });
            });

        if ($experienciaPorConsultor->isEmpty()) {
            return 0;
        }

        return round($experienciaPorConsultor->avg(), 1);
    }
}