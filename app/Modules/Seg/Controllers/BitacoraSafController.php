<?php

namespace App\Modules\Seg\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\SafCapacitacionImportacion;
use App\Modules\Fac\Models\SafInstructorImportacion;
use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Models\SincronizacionSafError;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BitacoraSafController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'desde' => [
                'nullable',
                'date',
            ],

            'hasta' => [
                'nullable',
                'date',
                'after_or_equal:desde',
            ],

            'estado' => [
                'nullable',
                Rule::in([
                    SincronizacionSaf::ESTADO_COMPLETADA,
                    SincronizacionSaf::ESTADO_COMPLETADA_CON_ERRORES,
                    SincronizacionSaf::ESTADO_FALLIDA,
                ]),
            ],
        ]);

        $consultaBase = SincronizacionSaf::query()
            ->when(
                filled($filtros['desde'] ?? null),
                fn (Builder $query) =>
                    $query->whereDate(
                        'fecha_inicio',
                        '>=',
                        $filtros['desde']
                    )
            )
            ->when(
                filled($filtros['hasta'] ?? null),
                fn (Builder $query) =>
                    $query->whereDate(
                        'fecha_inicio',
                        '<=',
                        $filtros['hasta']
                    )
            )
            ->when(
                filled($filtros['estado'] ?? null),
                fn (Builder $query) =>
                    $query->where(
                        'estado',
                        $filtros['estado']
                    )
            );

        $sincronizaciones = (clone $consultaBase)
            ->withCount([
                'errores',
                'erroresPendientes',
            ])
            ->latest('fecha_inicio')
            ->latest('id_sincronizacion_saf')
            ->paginate(15)
            ->withQueryString();

        $ultimaSincronizacion = SincronizacionSaf::query()
            ->latest('fecha_inicio')
            ->latest('id_sincronizacion_saf')
            ->first();

        $totalesPeriodo = (clone $consultaBase)
            ->selectRaw(
                'COUNT(*) AS total_sincronizaciones'
            )
            ->selectRaw(
                'COALESCE(SUM(consultores_creados), 0) AS consultores_creados'
            )
            ->selectRaw(
                'COALESCE(SUM(capacitaciones_creadas), 0) AS capacitaciones_creadas'
            )
            ->selectRaw(
                'COALESCE(SUM(total_registros_con_error), 0) AS registros_con_error'
            )
            ->selectRaw(
                'SUM(CASE WHEN estado IN (?, ?) THEN 1 ELSE 0 END) AS sincronizaciones_con_error',
                [
                    SincronizacionSaf::ESTADO_COMPLETADA_CON_ERRORES,
                    SincronizacionSaf::ESTADO_FALLIDA,
                ]
            )
            ->first();

        $tendencia = (clone $consultaBase)
            ->whereNotNull('fecha_inicio')
            ->selectRaw(
                'DATE(fecha_inicio) AS fecha'
            )
            ->selectRaw(
                'SUM(consultores_creados) AS consultores'
            )
            ->selectRaw(
                'SUM(capacitaciones_creadas) AS capacitaciones'
            )
            ->selectRaw(
                'SUM(total_registros_con_error) AS errores'
            )
            ->groupBy(
                DB::raw('DATE(fecha_inicio)')
            )
            ->orderBy(
                DB::raw('DATE(fecha_inicio)')
            )
            ->get()
            ->map(function ($item): array {
                return [
                    'fecha' => $item->fecha,
                    'consultores' =>
                        (int) $item->consultores,
                    'capacitaciones' =>
                        (int) $item->capacitaciones,
                    'errores' =>
                        (int) $item->errores,
                ];
            })
            ->values();

        $erroresPendientesRecientes =
            SincronizacionSafError::query()
                ->with('sincronizacion')
                ->pendientes()
                ->latest('created_at')
                ->limit(8)
                ->get();

        $estados = [
            SincronizacionSaf::ESTADO_COMPLETADA =>
                'Completada',

            SincronizacionSaf::ESTADO_COMPLETADA_CON_ERRORES =>
                'Completada con errores',

            SincronizacionSaf::ESTADO_FALLIDA =>
                'Fallida',
        ];

        return view(
            'seg.bitacora-saf.index',
            compact(
                'sincronizaciones',
                'ultimaSincronizacion',
                'totalesPeriodo',
                'tendencia',
                'erroresPendientesRecientes',
                'estados'
            )
        );
    }

    public function show(
        Request $request,
        SincronizacionSaf $sincronizacionSaf
    ): View {
        $filtros = $request->validate([
            'vista' => [
                'nullable',
                Rule::in([
                    'consultores',
                    'capacitaciones',
                    'errores',
                ]),
            ],
        ]);

        $vista = $filtros['vista']
            ?? (
                $sincronizacionSaf->tieneErrores()
                    ? 'errores'
                    : 'consultores'
            );

        $resumenConsultores =
            SafInstructorImportacion::query()
                ->deSincronizacion(
                    $sincronizacionSaf->getKey()
                )
                ->selectRaw('COUNT(*) AS total')
                ->selectRaw(
                    'SUM(CASE WHEN estado = ? THEN 1 ELSE 0 END) AS exitosos',
                    [
                        SafInstructorImportacion::ESTADO_PROCESADO,
                    ]
                )
                ->selectRaw(
                    'SUM(CASE WHEN estado = ? THEN 1 ELSE 0 END) AS errores',
                    [
                        SafInstructorImportacion::ESTADO_ERROR,
                    ]
                )
                ->first();

        $resumenCapacitaciones =
            SafCapacitacionImportacion::query()
                ->deSincronizacion(
                    $sincronizacionSaf->getKey()
                )
                ->selectRaw('COUNT(*) AS total')
                ->selectRaw(
                    'SUM(CASE WHEN estado = ? THEN 1 ELSE 0 END) AS exitosos',
                    [
                        SafCapacitacionImportacion::ESTADO_PROCESADO,
                    ]
                )
                ->selectRaw(
                    'SUM(CASE WHEN estado = ? THEN 1 ELSE 0 END) AS errores',
                    [
                        SafCapacitacionImportacion::ESTADO_ERROR,
                    ]
                )
                ->first();

        $totalErrores =
            $sincronizacionSaf
                ->errores()
                ->count();

        $erroresPendientes =
            $sincronizacionSaf
                ->errores()
                ->pendientes()
                ->count();

        $consultores = null;
        $capacitaciones = null;
        $errores = null;

        if ($vista === 'consultores') {
            $consultores =
                SafInstructorImportacion::query()
                    ->deSincronizacion(
                        $sincronizacionSaf->getKey()
                    )
                    ->orderBy('id_importacion')
                    ->paginate(
                        20,
                        ['*'],
                        'consultores_page'
                    )
                    ->withQueryString();
        }

        if ($vista === 'capacitaciones') {
            $capacitaciones =
                SafCapacitacionImportacion::query()
                    ->deSincronizacion(
                        $sincronizacionSaf->getKey()
                    )
                    ->orderBy('id_importacion')
                    ->paginate(
                        20,
                        ['*'],
                        'capacitaciones_page'
                    )
                    ->withQueryString();
        }

        if ($vista === 'errores') {
            $errores =
                $sincronizacionSaf
                    ->errores()
                    ->with('usuarioResolutor')
                    ->latest(
                        'id_sincronizacion_saf_error'
                    )
                    ->paginate(
                        20,
                        ['*'],
                        'errores_page'
                    )
                    ->withQueryString();
        }

        return view(
            'seg.bitacora-saf.show',
            compact(
                'sincronizacionSaf',
                'vista',
                'resumenConsultores',
                'resumenCapacitaciones',
                'totalErrores',
                'erroresPendientes',
                'consultores',
                'capacitaciones',
                'errores'
            )
        );
    }

    public function resolverError(
        Request $request,
        SincronizacionSaf $sincronizacionSaf,
        SincronizacionSafError $error
    ): RedirectResponse {
        abort_unless(
            $error->id_sincronizacion_saf
                === $sincronizacionSaf->getKey(),
            404
        );

        $datos = $request->validate([
            'observacion' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        $error->marcarComoResuelto(
            auth()->id(),
            $datos['observacion']
        );

        return redirect()
            ->route(
                'seg.bitacora-saf.show',
                [
                    'sincronizacionSaf' =>
                        $sincronizacionSaf,

                    'vista' =>
                        'errores',
                ]
            )
            ->with(
                'success',
                'El error fue marcado como resuelto.'
            );
    }

    public function reabrirError(
        SincronizacionSaf $sincronizacionSaf,
        SincronizacionSafError $error
    ): RedirectResponse {
        abort_unless(
            $error->id_sincronizacion_saf
                === $sincronizacionSaf->getKey(),
            404
        );

        $error->reabrir();

        return redirect()
            ->route(
                'seg.bitacora-saf.show',
                [
                    'sincronizacionSaf' =>
                        $sincronizacionSaf,

                    'vista' =>
                        'errores',
                ]
            )
            ->with(
                'success',
                'El error fue reabierto.'
            );
    }
}