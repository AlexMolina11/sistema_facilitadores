<?php

namespace App\Modules\Seg\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Models\SincronizacionSafError;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class BitacoraSafController extends Controller
{
    /**
     * Dashboard e historial general de sincronizaciones SAF.
     */
    public function index(Request $request): View
    {
        $this->validarFiltros($request);

        /*
         |--------------------------------------------------------------------------
         | Consulta principal del historial
         |--------------------------------------------------------------------------
         |
         | Esta consulta alimentará la tabla paginada del dashboard.
         | Se incluye el usuario ejecutor y se cuentan los errores pendientes
         | sin cargar todas las colecciones completas.
         |
         */
        $sincronizaciones = SincronizacionSaf::query()
            ->with([
                'usuarioEjecutor:id_usuario,nombres,apellidos,email',
            ])
            ->withCount([
                'errores',
                'erroresPendientes',
            ])
            ->when(
                $request->filled('estado'),
                fn (Builder $query): Builder => $query->where(
                    'estado',
                    $request->string('estado')->toString()
                )
            )
            ->when(
                $request->filled('tipo_ejecucion'),
                fn (Builder $query): Builder => $query->where(
                    'tipo_ejecucion',
                    $request->string('tipo_ejecucion')->toString()
                )
            )
            ->when(
                $request->boolean('solo_con_errores'),
                fn (Builder $query): Builder => $query->conErrores()
            )
            ->when(
                $request->filled('desde'),
                fn (Builder $query): Builder => $query->whereDate(
                    'fecha_inicio',
                    '>=',
                    $request->date('desde')
                )
            )
            ->when(
                $request->filled('hasta'),
                fn (Builder $query): Builder => $query->whereDate(
                    'fecha_inicio',
                    '<=',
                    $request->date('hasta')
                )
            )
            ->when(
                $request->filled('q'),
                function (Builder $query) use ($request): void {
                    $buscar = trim($request->string('q')->toString());

                    $query->where(function (Builder $subquery) use ($buscar): void {
                        $subquery
                            ->where('uuid', 'like', "%{$buscar}%")
                            ->orWhere('mensaje', 'like', "%{$buscar}%")
                            ->orWhereHas(
                                'usuarioEjecutor',
                                function (Builder $usuarioQuery) use ($buscar): void {
                                    $usuarioQuery
                                        ->where('nombres', 'like', "%{$buscar}%")
                                        ->orWhere('apellidos', 'like', "%{$buscar}%")
                                        ->orWhere('email', 'like', "%{$buscar}%");
                                }
                            );
                    });
                }
            )
            ->latest('fecha_inicio')
            ->latest('id_sincronizacion_saf')
            ->paginate(20)
            ->withQueryString();

        /*
         |--------------------------------------------------------------------------
         | Indicadores globales
         |--------------------------------------------------------------------------
         |
         | Estos indicadores representan el histórico completo y no solamente
         | la página actual de resultados.
         |
         */
        $resumenGeneral = SincronizacionSaf::query()
            ->selectRaw('COUNT(*) as total_sincronizaciones')
            ->selectRaw(
                'SUM(CASE WHEN estado = ? THEN 1 ELSE 0 END) as completadas',
                [SincronizacionSaf::ESTADO_COMPLETADA]
            )
            ->selectRaw(
                'SUM(CASE WHEN estado = ? THEN 1 ELSE 0 END) as completadas_con_errores',
                [SincronizacionSaf::ESTADO_COMPLETADA_CON_ERRORES]
            )
            ->selectRaw(
                'SUM(CASE WHEN estado = ? THEN 1 ELSE 0 END) as fallidas',
                [SincronizacionSaf::ESTADO_FALLIDA]
            )
            ->selectRaw(
                'COALESCE(SUM(consultores_creados), 0) as consultores_creados'
            )
            ->selectRaw(
                'COALESCE(SUM(consultores_actualizados), 0) as consultores_actualizados'
            )
            ->selectRaw(
                'COALESCE(SUM(consultores_sin_cambios), 0) as consultores_sin_cambios'
            )
            ->selectRaw(
                'COALESCE(SUM(consultores_con_error), 0) as consultores_con_error'
            )
            ->selectRaw(
                'COALESCE(SUM(capacitaciones_creadas), 0) as capacitaciones_creadas'
            )
            ->selectRaw(
                'COALESCE(SUM(capacitaciones_actualizadas), 0) as capacitaciones_actualizadas'
            )
            ->selectRaw(
                'COALESCE(SUM(capacitaciones_con_error), 0) as capacitaciones_con_error'
            )
            ->first();

        /*
         |--------------------------------------------------------------------------
         | Resumen de errores
         |--------------------------------------------------------------------------
         */
        $resumenErrores = SincronizacionSafError::query()
            ->selectRaw('COUNT(*) as total_errores')
            ->selectRaw(
                'SUM(CASE WHEN resuelto = 0 THEN 1 ELSE 0 END) as pendientes'
            )
            ->selectRaw(
                'SUM(CASE WHEN resuelto = 1 THEN 1 ELSE 0 END) as resueltos'
            )
            ->selectRaw(
                'SUM(CASE WHEN tipo_registro = ? THEN 1 ELSE 0 END) as errores_consultores',
                [SincronizacionSafError::TIPO_REGISTRO_CONSULTOR]
            )
            ->selectRaw(
                'SUM(CASE WHEN tipo_registro = ? THEN 1 ELSE 0 END) as errores_capacitaciones',
                [SincronizacionSafError::TIPO_REGISTRO_CAPACITACION]
            )
            ->selectRaw(
                'SUM(CASE WHEN tipo_registro = ? THEN 1 ELSE 0 END) as errores_generales',
                [SincronizacionSafError::TIPO_REGISTRO_GENERAL]
            )
            ->first();

        /*
         |--------------------------------------------------------------------------
         | Última ejecución
         |--------------------------------------------------------------------------
         */
        $ultimaSincronizacion = SincronizacionSaf::query()
            ->with([
                'usuarioEjecutor:id_usuario,nombres,apellidos,email',
            ])
            ->withCount([
                'errores',
                'erroresPendientes',
            ])
            ->latest('fecha_inicio')
            ->latest('id_sincronizacion_saf')
            ->first();

        /*
         |--------------------------------------------------------------------------
         | Catálogos locales para los filtros
         |--------------------------------------------------------------------------
         */
        $estados = [
            SincronizacionSaf::ESTADO_PENDIENTE => 'Pendiente',
            SincronizacionSaf::ESTADO_EN_PROCESO => 'En proceso',
            SincronizacionSaf::ESTADO_COMPLETADA => 'Completada',
            SincronizacionSaf::ESTADO_COMPLETADA_CON_ERRORES => 'Completada con errores',
            SincronizacionSaf::ESTADO_FALLIDA => 'Fallida',
        ];

        $tiposEjecucion = [
            SincronizacionSaf::TIPO_AUTOMATICA => 'Automática',
            SincronizacionSaf::TIPO_MANUAL => 'Manual',
        ];

        return view('seg.bitacora-saf.index', compact(
            'sincronizaciones',
            'resumenGeneral',
            'resumenErrores',
            'ultimaSincronizacion',
            'estados',
            'tiposEjecucion'
        ));
    }

    /**
     * Detalle individual de una sincronización.
     */
    public function show(
        Request $request,
        SincronizacionSaf $sincronizacionSaf
    ): View {
        $filtros = $request->validate([
            'q' => [
                'nullable',
                'string',
                'max:150',
            ],

            'tipo_registro' => [
                'nullable',
                Rule::in([
                    SincronizacionSafError::TIPO_REGISTRO_CONSULTOR,
                    SincronizacionSafError::TIPO_REGISTRO_CAPACITACION,
                    SincronizacionSafError::TIPO_REGISTRO_GENERAL,
                ]),
            ],

            'resuelto' => [
                'nullable',
                Rule::in([
                    '0',
                    '1',
                ]),
            ],
        ]);

        $sincronizacionSaf->load([
            'usuarioEjecutor',
        ]);

        $consultaErrores = $sincronizacionSaf
            ->errores()
            ->orderByDesc('id_sincronizacion_saf_error');

        if (filled($filtros['q'] ?? null)) {
            $busqueda = trim($filtros['q']);

            $consultaErrores->where(function ($query) use ($busqueda) {
                $query
                    ->where(
                        'mensaje_error',
                        'like',
                        "%{$busqueda}%"
                    )
                    ->orWhere(
                        'codigo_error',
                        'like',
                        "%{$busqueda}%"
                    )
                    ->orWhere(
                        'identificador_externo',
                        'like',
                        "%{$busqueda}%"
                    )
                    ->orWhere(
                        'campo_error',
                        'like',
                        "%{$busqueda}%"
                    );
            });
        }

        if (filled($filtros['tipo_registro'] ?? null)) {
            $consultaErrores->where(
                'tipo_registro',
                $filtros['tipo_registro']
            );
        }

        if (
            array_key_exists('resuelto', $filtros)
            && $filtros['resuelto'] !== null
            && $filtros['resuelto'] !== ''
        ) {
            $consultaErrores->where(
                'resuelto',
                (bool) ((int) $filtros['resuelto'])
            );
        }

        $errores = $consultaErrores
            ->paginate(15)
            ->withQueryString();

        $resumenErrores = $sincronizacionSaf
            ->errores()
            ->selectRaw('COUNT(*) as total_errores')
            ->selectRaw(
                'SUM(CASE WHEN resuelto = 0 THEN 1 ELSE 0 END) as pendientes'
            )
            ->selectRaw(
                'SUM(CASE WHEN resuelto = 1 THEN 1 ELSE 0 END) as resueltos'
            )
            ->selectRaw(
                'SUM(CASE WHEN tipo_registro = ? THEN 1 ELSE 0 END) as errores_consultores',
                [
                    SincronizacionSafError::TIPO_REGISTRO_CONSULTOR,
                ]
            )
            ->selectRaw(
                'SUM(CASE WHEN tipo_registro = ? THEN 1 ELSE 0 END) as errores_capacitaciones',
                [
                    SincronizacionSafError::TIPO_REGISTRO_CAPACITACION,
                ]
            )
            ->selectRaw(
                'SUM(CASE WHEN tipo_registro = ? THEN 1 ELSE 0 END) as errores_generales',
                [
                    SincronizacionSafError::TIPO_REGISTRO_GENERAL,
                ]
            )
            ->first();

        $tiposRegistro = [
            SincronizacionSafError::TIPO_REGISTRO_CONSULTOR =>
                'Consultor',

            SincronizacionSafError::TIPO_REGISTRO_CAPACITACION =>
                'Capacitación',

            SincronizacionSafError::TIPO_REGISTRO_GENERAL =>
                'General',
        ];

        return view('seg.bitacora-saf.show', [
            'sincronizacionSaf' => $sincronizacionSaf,
            'errores' => $errores,
            'resumenErrores' => $resumenErrores,
            'tiposRegistro' => $tiposRegistro,
        ]);
    }

    /**
     * Valida los filtros disponibles en el dashboard general.
     */
    private function validarFiltros(Request $request): void
    {
        $request->validate([
            'q' => [
                'nullable',
                'string',
                'max:150',
            ],
            'estado' => [
                'nullable',
                'in:' . implode(',', [
                    SincronizacionSaf::ESTADO_PENDIENTE,
                    SincronizacionSaf::ESTADO_EN_PROCESO,
                    SincronizacionSaf::ESTADO_COMPLETADA,
                    SincronizacionSaf::ESTADO_COMPLETADA_CON_ERRORES,
                    SincronizacionSaf::ESTADO_FALLIDA,
                ]),
            ],
            'tipo_ejecucion' => [
                'nullable',
                'in:' . implode(',', [
                    SincronizacionSaf::TIPO_MANUAL,
                    SincronizacionSaf::TIPO_AUTOMATICA,
                ]),
            ],
            'solo_con_errores' => [
                'nullable',
                'boolean',
            ],
            'desde' => [
                'nullable',
                'date',
            ],
            'hasta' => [
                'nullable',
                'date',
                'after_or_equal:desde',
            ],
        ]);
    }
}