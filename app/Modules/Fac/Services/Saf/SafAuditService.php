<?php

namespace App\Modules\Fac\Services\Saf;

use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Models\SincronizacionSafError;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use LogicException;
use Throwable;

class SafAuditService
{
    /**
     * Inicia una nueva ejecución de sincronización.
     */
    public function iniciar(
        string $tipoEjecucion,
        Authenticatable|int|null $usuario = null,
        array $resumenInicial = []
    ): SincronizacionSaf {
        $this->verificarAuditoriaHabilitada();
        $this->validarTipoEjecucion($tipoEjecucion);

        return DB::transaction(function () use (
            $tipoEjecucion,
            $usuario,
            $resumenInicial
        ): SincronizacionSaf {
            $sincronizacion = new SincronizacionSaf();

            $sincronizacion->forceFill([
                'uuid' => (string) Str::uuid(),
                'tipo_ejecucion' => $tipoEjecucion,
                'estado' => SincronizacionSaf::ESTADO_EN_PROCESO,
                'fecha_inicio' => now(),
                'fecha_fin' => null,

                'total_registros_recibidos' => 0,
                'total_registros_procesados' => 0,
                'total_registros_exitosos' => 0,
                'total_registros_con_error' => 0,

                'consultores_creados' => 0,
                'consultores_actualizados' => 0,
                'consultores_sin_cambios' => 0,
                'consultores_con_error' => 0,

                'capacitaciones_creadas' => 0,
                'capacitaciones_actualizadas' => 0,
                'capacitaciones_sin_cambios' => 0,
                'capacitaciones_desactivadas' => 0,
                'capacitaciones_con_error' => 0,

                'mensaje' => null,

                'usuario_ejecuta' => $this->resolverUsuarioId(
                    $usuario
                ),

                'resumen' => $resumenInicial !== []
                    ? $resumenInicial
                    : null,
            ]);

            $sincronizacion->save();

            return $sincronizacion->fresh();
        });
    }

    /**
     * Define el total de registros recibidos desde SAF.
     */
    public function establecerTotal(
        SincronizacionSaf $sincronizacion,
        int $total
    ): SincronizacionSaf {
        $this->verificarEjecucionEditable($sincronizacion);

        if ($total < 0) {
            throw new InvalidArgumentException(
                'El total de registros no puede ser negativo.'
            );
        }

        $sincronizacion->forceFill([
            'total_registros_recibidos' => $total,
        ])->save();

        return $sincronizacion->fresh();
    }

    /**
     * Incrementa únicamente el total de registros procesados.
     */
    public function registrarProcesado(
        SincronizacionSaf $sincronizacion,
        int $cantidad = 1
    ): SincronizacionSaf {
        return $this->incrementar(
            $sincronizacion,
            'total_registros_procesados',
            $cantidad
        );
    }

    /**
     * Registra consultores creados.
     */
    public function registrarConsultorCreado(
        SincronizacionSaf $sincronizacion,
        int $cantidad = 1
    ): SincronizacionSaf {
        return $this->incrementarMultiples(
            $sincronizacion,
            [
                'consultores_creados' => $cantidad,
                'total_registros_procesados' => $cantidad,
                'total_registros_exitosos' => $cantidad,
            ]
        );
    }

    /**
     * Registra consultores actualizados.
     */
    public function registrarConsultorActualizado(
        SincronizacionSaf $sincronizacion,
        int $cantidad = 1
    ): SincronizacionSaf {
        return $this->incrementarMultiples(
            $sincronizacion,
            [
                'consultores_actualizados' => $cantidad,
                'total_registros_procesados' => $cantidad,
                'total_registros_exitosos' => $cantidad,
            ]
        );
    }

    /**
     * Registra consultores que no necesitaron cambios.
     */
    public function registrarConsultorSinCambios(
        SincronizacionSaf $sincronizacion,
        int $cantidad = 1
    ): SincronizacionSaf {
        return $this->incrementarMultiples(
            $sincronizacion,
            [
                'consultores_sin_cambios' => $cantidad,
                'total_registros_procesados' => $cantidad,
                'total_registros_exitosos' => $cantidad,
            ]
        );
    }

    /**
     * Registra consultores que presentaron error.
     */
    public function registrarConsultorConError(
        SincronizacionSaf $sincronizacion,
        int $cantidad = 1
    ): SincronizacionSaf {
        return $this->incrementarMultiples(
            $sincronizacion,
            [
                'consultores_con_error' => $cantidad,
                'total_registros_procesados' => $cantidad,
                'total_registros_con_error' => $cantidad,
            ]
        );
    }

    /**
     * Registra capacitaciones creadas.
     */
    public function registrarCapacitacionCreada(
        SincronizacionSaf $sincronizacion,
        int $cantidad = 1
    ): SincronizacionSaf {
        return $this->incrementarMultiples(
            $sincronizacion,
            [
                'capacitaciones_creadas' => $cantidad,
                'total_registros_procesados' => $cantidad,
                'total_registros_exitosos' => $cantidad,
            ]
        );
    }

    /**
     * Registra capacitaciones actualizadas.
     */
    public function registrarCapacitacionActualizada(
        SincronizacionSaf $sincronizacion,
        int $cantidad = 1
    ): SincronizacionSaf {
        return $this->incrementarMultiples(
            $sincronizacion,
            [
                'capacitaciones_actualizadas' => $cantidad,
                'total_registros_procesados' => $cantidad,
                'total_registros_exitosos' => $cantidad,
            ]
        );
    }

    /**
     * Registra capacitaciones que no necesitaron cambios.
     */
    public function registrarCapacitacionSinCambios(
        SincronizacionSaf $sincronizacion,
        int $cantidad = 1
    ): SincronizacionSaf {
        return $this->incrementarMultiples(
            $sincronizacion,
            [
                'capacitaciones_sin_cambios' => $cantidad,
                'total_registros_procesados' => $cantidad,
                'total_registros_exitosos' => $cantidad,
            ]
        );
    }

    /**
     * Registra capacitaciones desactivadas.
     */
    public function registrarCapacitacionDesactivada(
        SincronizacionSaf $sincronizacion,
        int $cantidad = 1
    ): SincronizacionSaf {
        return $this->incrementarMultiples(
            $sincronizacion,
            [
                'capacitaciones_desactivadas' => $cantidad,
                'total_registros_procesados' => $cantidad,
                'total_registros_exitosos' => $cantidad,
            ]
        );
    }

    /**
     * Registra capacitaciones que presentaron error.
     */
    public function registrarCapacitacionConError(
        SincronizacionSaf $sincronizacion,
        int $cantidad = 1
    ): SincronizacionSaf {
        return $this->incrementarMultiples(
            $sincronizacion,
            [
                'capacitaciones_con_error' => $cantidad,
                'total_registros_procesados' => $cantidad,
                'total_registros_con_error' => $cantidad,
            ]
        );
    }

    /**
     * Registra el detalle de un error individual.
     *
     * Este método no incrementa los contadores generales, porque un mismo
     * registro podría generar más de un detalle de error.
     */
    public function registrarError(
        SincronizacionSaf $sincronizacion,
        string $tipoRegistro,
        string $tipoOperacion,
        string $mensaje,
        array $opciones = []
    ): SincronizacionSafError {
        $this->verificarAuditoriaHabilitada();
        $this->verificarEjecucionEditable($sincronizacion);
        $this->validarTipoRegistro($tipoRegistro);
        $this->validarTipoOperacion($tipoOperacion);

        return DB::transaction(function () use (
            $sincronizacion,
            $tipoRegistro,
            $tipoOperacion,
            $mensaje,
            $opciones
        ): SincronizacionSafError {
            $registro = SincronizacionSaf::query()
                ->lockForUpdate()
                ->findOrFail($sincronizacion->getKey());

            $error = $registro->errores()->create([
                'tipo_registro' => $tipoRegistro,
                'tipo_operacion' => $tipoOperacion,

                'id_registro_externo' => Arr::get(
                    $opciones,
                    'id_registro_externo'
                ),

                'id_registro_local' => Arr::get(
                    $opciones,
                    'id_registro_local'
                ),

                'codigo_error' => Arr::get(
                    $opciones,
                    'codigo_error'
                ),

                'mensaje' => $mensaje,

                'detalle_tecnico' => $this->detalleTecnico(
                    $opciones
                ),

                'excepcion' => Arr::get(
                    $opciones,
                    'excepcion'
                ),

                'archivo' => Arr::get(
                    $opciones,
                    'archivo'
                ),

                'linea' => Arr::get(
                    $opciones,
                    'linea'
                ),

                'datos_recibidos' => $this->datosParaAuditoria(
                    Arr::get($opciones, 'datos_recibidos', [])
                ),

                'resuelto' => false,
                'fecha_resolucion' => null,
                'usuario_resuelve' => null,
                'observacion_resolucion' => null,
            ]);

            return $error->fresh();
        });
    }

    /**
     * Registra una excepción como detalle de error.
     */
    public function registrarExcepcion(
        SincronizacionSaf $sincronizacion,
        Throwable $exception,
        string $tipoRegistro =
            SincronizacionSafError::TIPO_REGISTRO_GENERAL,
        string $tipoOperacion =
            SincronizacionSafError::OPERACION_PROCESAR,
        array $datosRecibidos = [],
        string|int|null $idRegistroExterno = null,
        ?int $idRegistroLocal = null
    ): SincronizacionSafError {
        return $this->registrarError(
            sincronizacion: $sincronizacion,
            tipoRegistro: $tipoRegistro,
            tipoOperacion: $tipoOperacion,
            mensaje: $exception->getMessage(),
            opciones: [
                'id_registro_externo' => $idRegistroExterno,
                'id_registro_local' => $idRegistroLocal,
                'codigo_error' => (string) $exception->getCode(),
                'excepcion' => $exception::class,
                'archivo' => $exception->getFile(),
                'linea' => $exception->getLine(),
                'detalle_tecnico' => $exception->getTraceAsString(),
                'datos_recibidos' => $datosRecibidos,
            ]
        );
    }

    /**
     * Finaliza una ejecución.
     *
     * Sin errores: COMPLETADA.
     * Con errores: COMPLETADA_CON_ERRORES.
     */
    public function finalizar(
        SincronizacionSaf $sincronizacion,
        array $resumen = []
    ): SincronizacionSaf {
        $this->verificarEjecucionEditable($sincronizacion);

        $sincronizacion->refresh();

        $tieneErrores = $sincronizacion->tieneErrores()
            || $sincronizacion->errores()->exists();

        $estado = $tieneErrores
            ? SincronizacionSaf::ESTADO_COMPLETADA_CON_ERRORES
            : SincronizacionSaf::ESTADO_COMPLETADA;

        $mensaje = $tieneErrores
            ? 'La sincronización finalizó con errores.'
            : 'La sincronización finalizó correctamente.';

        return $this->cerrar(
            sincronizacion: $sincronizacion,
            estado: $estado,
            mensaje: $mensaje,
            resumen: $resumen
        );
    }

    /**
     * Marca toda la ejecución como fallida.
     */
    public function marcarComoFallida(
        SincronizacionSaf $sincronizacion,
        Throwable|string $motivo,
        array $resumen = []
    ): SincronizacionSaf {
        $this->verificarEjecucionEditable($sincronizacion);

        $mensaje = $motivo instanceof Throwable
            ? $motivo->getMessage()
            : $motivo;

        return $this->cerrar(
            sincronizacion: $sincronizacion,
            estado: SincronizacionSaf::ESTADO_FALLIDA,
            mensaje: $mensaje,
            resumen: array_merge(
                $resumen,
                [
                    'motivo_fallo' => $mensaje,
                ]
            )
        );
    }

    /**
     * Actualiza el resumen sin cerrar la ejecución.
     */
    public function actualizarResumen(
        SincronizacionSaf $sincronizacion,
        array $datos
    ): SincronizacionSaf {
        $this->verificarEjecucionEditable($sincronizacion);

        $sincronizacion->refresh();

        $sincronizacion->forceFill([
            'resumen' => array_merge(
                $sincronizacion->resumen ?? [],
                $datos
            ),
        ])->save();

        return $sincronizacion->fresh();
    }

    /**
     * Elimina datos sensibles antes de almacenarlos.
     */
    public function datosParaAuditoria(array $datos): array
    {
        if (! config('saf.audit.store_error_payload', true)) {
            return [];
        }

        $camposSensibles = collect(
            config('saf.sensitive_fields', [])
        )
            ->map(
                static fn (mixed $campo): string =>
                    Str::lower(trim((string) $campo))
            )
            ->filter()
            ->values()
            ->all();

        return $this->sanitizarRecursivamente(
            $datos,
            $camposSensibles
        );
    }

    /**
     * Cierra la ejecución con un estado definitivo.
     */
    private function cerrar(
        SincronizacionSaf $sincronizacion,
        string $estado,
        string $mensaje,
        array $resumen = []
    ): SincronizacionSaf {
        return DB::transaction(function () use (
            $sincronizacion,
            $estado,
            $mensaje,
            $resumen
        ): SincronizacionSaf {
            $registro = SincronizacionSaf::query()
                ->lockForUpdate()
                ->findOrFail($sincronizacion->getKey());

            if (
                $registro->estado
                !== SincronizacionSaf::ESTADO_EN_PROCESO
            ) {
                throw new LogicException(
                    'La sincronización ya no se encuentra en proceso.'
                );
            }

            $registro->forceFill([
                'estado' => $estado,
                'fecha_fin' => now(),
                'mensaje' => $mensaje,

                'resumen' => array_merge(
                    $registro->resumen ?? [],
                    $resumen,
                    [
                        'finalizada_en' =>
                            now()->toIso8601String(),
                    ]
                ),
            ])->save();

            return $registro->fresh();
        });
    }

    /**
     * Incrementa un contador individual.
     */
    private function incrementar(
        SincronizacionSaf $sincronizacion,
        string $campo,
        int $cantidad
    ): SincronizacionSaf {
        return $this->incrementarMultiples(
            $sincronizacion,
            [
                $campo => $cantidad,
            ]
        );
    }

    /**
     * Incrementa varios contadores de forma atómica.
     */
    private function incrementarMultiples(
        SincronizacionSaf $sincronizacion,
        array $contadores
    ): SincronizacionSaf {
        $this->verificarEjecucionEditable($sincronizacion);

        foreach ($contadores as $cantidad) {
            if (! is_int($cantidad) || $cantidad <= 0) {
                throw new InvalidArgumentException(
                    'La cantidad debe ser un entero mayor que cero.'
                );
            }
        }

        DB::transaction(function () use (
            $sincronizacion,
            $contadores
        ): void {
            $registro = SincronizacionSaf::query()
                ->lockForUpdate()
                ->findOrFail($sincronizacion->getKey());

            if (
                $registro->estado
                !== SincronizacionSaf::ESTADO_EN_PROCESO
            ) {
                throw new LogicException(
                    'La sincronización ya no se encuentra en proceso.'
                );
            }

            foreach ($contadores as $campo => $cantidad) {
                $registro->{$campo} =
                    (int) $registro->{$campo} + $cantidad;
            }

            $registro->save();
        });

        return $sincronizacion->fresh();
    }

    /**
     * Impide modificar una ejecución finalizada.
     */
    private function verificarEjecucionEditable(
        SincronizacionSaf $sincronizacion
    ): void {
        $sincronizacion->refresh();

        if (! $sincronizacion->estaEnProceso()) {
            throw new LogicException(
                'La sincronización ya no se encuentra en proceso.'
            );
        }
    }

    /**
     * Verifica que la auditoría esté habilitada.
     */
    private function verificarAuditoriaHabilitada(): void
    {
        if (! config('saf.audit.enabled', true)) {
            throw new LogicException(
                'La auditoría de la integración SAF está deshabilitada.'
            );
        }
    }

    /**
     * Valida el tipo de ejecución.
     */
    private function validarTipoEjecucion(
        string $tipoEjecucion
    ): void {
        $tiposPermitidos = [
            SincronizacionSaf::TIPO_MANUAL,
            SincronizacionSaf::TIPO_AUTOMATICA,
        ];

        if (! in_array($tipoEjecucion, $tiposPermitidos, true)) {
            throw new InvalidArgumentException(
                'El tipo de ejecución SAF no es válido.'
            );
        }
    }

    /**
     * Valida el tipo de registro del error.
     */
    private function validarTipoRegistro(
        string $tipoRegistro
    ): void {
        $tiposPermitidos = [
            SincronizacionSafError::TIPO_REGISTRO_GENERAL,
            SincronizacionSafError::TIPO_REGISTRO_CONSULTOR,
            SincronizacionSafError::TIPO_REGISTRO_CAPACITACION,
        ];

        if (! in_array($tipoRegistro, $tiposPermitidos, true)) {
            throw new InvalidArgumentException(
                'El tipo de registro SAF no es válido.'
            );
        }
    }

    /**
     * Valida el tipo de operación del error.
     */
    private function validarTipoOperacion(
        string $tipoOperacion
    ): void {
        $operacionesPermitidas = [
            SincronizacionSafError::OPERACION_CONSULTAR,
            SincronizacionSafError::OPERACION_CREAR,
            SincronizacionSafError::OPERACION_ACTUALIZAR,
            SincronizacionSafError::OPERACION_DESACTIVAR,
            SincronizacionSafError::OPERACION_VALIDAR,
            SincronizacionSafError::OPERACION_PROCESAR,
        ];

        if (
            ! in_array(
                $tipoOperacion,
                $operacionesPermitidas,
                true
            )
        ) {
            throw new InvalidArgumentException(
                'La operación SAF no es válida.'
            );
        }
    }

    /**
     * Obtiene el identificador del usuario.
     */
    private function resolverUsuarioId(
        Authenticatable|int|null $usuario
    ): ?int {
        if (is_int($usuario)) {
            return $usuario;
        }

        if ($usuario instanceof Authenticatable) {
            return (int) $usuario->getAuthIdentifier();
        }

        $idUsuario = auth()->id();

        return $idUsuario !== null
            ? (int) $idUsuario
            : null;
    }

    /**
     * Devuelve el detalle técnico cuando está permitido.
     */
    private function detalleTecnico(array $opciones): ?string
    {
        if (
            ! config(
                'saf.audit.store_exception_details',
                true
            )
        ) {
            return null;
        }

        return Arr::get($opciones, 'detalle_tecnico');
    }

    /**
     * Protege recursivamente los campos sensibles.
     */
    private function sanitizarRecursivamente(
        array $datos,
        array $camposSensibles
    ): array {
        foreach ($datos as $clave => $valor) {
            $claveNormalizada = Str::lower(
                trim((string) $clave)
            );

            if (
                in_array(
                    $claveNormalizada,
                    $camposSensibles,
                    true
                )
            ) {
                $datos[$clave] = '[PROTEGIDO]';

                continue;
            }

            if (is_array($valor)) {
                $datos[$clave] =
                    $this->sanitizarRecursivamente(
                        $valor,
                        $camposSensibles
                    );
            }
        }

        return $datos;
    }
}