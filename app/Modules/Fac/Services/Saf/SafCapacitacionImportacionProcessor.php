<?php

namespace App\Modules\Fac\Services\Saf;

use App\Modules\Fac\Data\Saf\CapacitacionSafData;
use App\Modules\Fac\Models\SafCapacitacionImportacion;
use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Models\SincronizacionSafError;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class SafCapacitacionImportacionProcessor
{
    public function __construct(
        private readonly SafCapacitacionSyncService $sincronizacion,
        private readonly SafAuditService $auditoria
    ) {}

    /**
     * Procesa las capacitaciones pendientes.
     *
     * @return array{
     *     detectados: int,
     *     procesados: int,
     *     exitosos: int,
     *     con_error: int
     * }
     */
    public function procesar(
        SincronizacionSaf $ejecucion,
        ?int $limite = null
    ): array {
        $resumen = [
            'detectados' => 0,
            'procesados' => 0,
            'exitosos' => 0,
            'con_error' => 0,
        ];

        $consulta = SafCapacitacionImportacion::query()
            ->pendientes()
            ->orderBy('id_importacion');

        if ($limite !== null && $limite > 0) {
            $consulta->limit($limite);
        }

        $ids = $consulta->pluck(
            'id_importacion'
        );

        $resumen['detectados'] =
            $ids->count();

        foreach ($ids as $idImportacion) {
            $registro = $this->tomarRegistro(
                (int) $idImportacion,
                $ejecucion
            );

            if ($registro === null) {
                continue;
            }

            $exitoso = $this->procesarRegistro(
                $registro,
                $ejecucion
            );

            $resumen['procesados']++;

            if ($exitoso) {
                $resumen['exitosos']++;
            } else {
                $resumen['con_error']++;
            }
        }

        return $resumen;
    }

    /**
     * Reserva un registro pendiente.
     */
    private function tomarRegistro(
        int $idImportacion,
        SincronizacionSaf $ejecucion
    ): ?SafCapacitacionImportacion {
        return DB::transaction(
            function () use (
                $idImportacion,
                $ejecucion
            ): ?SafCapacitacionImportacion {
                $registro =
                    SafCapacitacionImportacion::query()
                        ->lockForUpdate()
                        ->find($idImportacion);

                if (
                    $registro === null
                    || ! $registro->estaPendiente()
                ) {
                    return null;
                }

                $registro->forceFill([
                    'estado' =>
                        SafCapacitacionImportacion::ESTADO_EN_PROCESO,

                    'resultado_procesamiento' =>
                        null,

                    'intentos' =>
                        (int) $registro->intentos + 1,

                    'mensaje_error' =>
                        null,

                    'fecha_procesamiento' =>
                        null,

                    'id_sincronizacion' =>
                        $ejecucion->getKey(),

                    'id_registro_local' =>
                        null,
                ])->save();

                return $registro->fresh();
            }
        );
    }

    /**
     * Convierte el registro staging en un DTO
     * y ejecuta la sincronización.
     */
    private function procesarRegistro(
        SafCapacitacionImportacion $registro,
        SincronizacionSaf $ejecucion
    ): bool {
        try {
            $capacitacion =
                CapacitacionSafData::fromArray([
                    'id_instructor' =>
                        $registro->id_instructor,

                    'programa_curso_id' =>
                        $registro->programa_curso_id,

                    'codigo_evento' =>
                        $registro->codigo_evento,

                    'curso_nombre' =>
                        $registro->curso_nombre,

                    'fecha_inicio' =>
                        $registro->fecha_inicio
                            ?->format('Y-m-d'),

                    'fecha_fin' =>
                        $registro->fecha_fin
                            ?->format('Y-m-d'),

                    'estado_curso_nombre' =>
                        $registro->estado_curso_nombre,

                    'no_horas_real' =>
                        $registro->no_horas_real,

                    'modalidad' =>
                        $registro->modalidad,

                    'tipo_evento_nombre' =>
                        $registro->tipo_evento_nombre,

                    'cliente' =>
                        $registro->cliente,

                    'encuesta_id' =>
                        $registro->encuesta_id,

                    'encuesta_nombre' =>
                        $registro->encuesta_nombre,

                    'promedio_encuesta' =>
                        $registro->promedio_encuesta,

                    'fecha_evaluacion' =>
                        $registro->fecha_evaluacion
                            ?->format('Y-m-d H:i:s'),
                ]);

            $resultado =
                $this->sincronizacion->sincronizar(
                    $capacitacion,
                    $ejecucion
                );

            /*
             * RESULTADO_DESACTIVADO se conserva temporalmente
             * porque todavía existe en el servicio actual.
             *
             * En el Bloque 4 revisaremos si debe desaparecer
             * definitivamente del contrato del servicio.
             */
            if (
                in_array(
                    $resultado['resultado'],
                    [
                        SafCapacitacionSyncService::RESULTADO_CREADO,
                        SafCapacitacionSyncService::RESULTADO_ACTUALIZADO,
                        SafCapacitacionSyncService::RESULTADO_SIN_CAMBIOS,
                        SafCapacitacionSyncService::RESULTADO_DESACTIVADO,
                    ],
                    true
                )
            ) {
                $this->marcarProcesado(
                    registro:
                        $registro,

                    resultado:
                        $resultado['resultado'],

                    idRegistroLocal:
                        isset(
                            $resultado['capacitacion']
                                ->id_capacitacion_fepade
                        )
                            ? (int) $resultado['capacitacion']
                                ->id_capacitacion_fepade
                            : null
                );

                return true;
            }

            /*
             * Los errores producidos dentro del servicio
             * después de construir el DTO ya son registrados
             * por el propio servicio de sincronización.
             */
            $this->marcarError(
                $registro,

                $resultado['mensaje']
                    ?? 'La capacitación no pudo ser sincronizada.'
            );

            return false;
        } catch (ValidationException $exception) {
            $mensaje =
                $this->mensajeValidacion(
                    $exception
                );

            $this->marcarError(
                $registro,
                $mensaje
            );

            $this->registrarErroresValidacion(
                $registro,
                $ejecucion,
                $exception
            );

            return false;
        } catch (Throwable $exception) {
            $this->marcarError(
                $registro,
                $exception->getMessage()
            );

            $this->registrarExcepcion(
                $registro,
                $ejecucion,
                $exception
            );

            return false;
        }
    }

    /**
     * Registra todos los mensajes generados
     * por la validación del DTO.
     */
    private function registrarErroresValidacion(
        SafCapacitacionImportacion $registro,
        SincronizacionSaf $ejecucion,
        ValidationException $exception
    ): void {
        /*
         * El contador aumenta una única vez
         * por registro de capacitación.
         */
        $this->auditoria
            ->registrarCapacitacionConError(
                $ejecucion
            );

        foreach (
            $exception->errors()
            as $campo => $mensajes
        ) {
            foreach ($mensajes as $mensaje) {
                $this->auditoria->registrarError(
                    sincronizacion:
                        $ejecucion,

                    tipoRegistro:
                        SincronizacionSafError::TIPO_REGISTRO_CAPACITACION,

                    tipoOperacion:
                        SincronizacionSafError::OPERACION_VALIDAR,

                    mensaje:
                        (string) $mensaje,

                    opciones: [
                        'id_registro_externo' =>
                            $this->identificadorExterno(
                                $registro
                            ),

                        'id_registro_local' =>
                            $registro->id_importacion,

                        'codigo_error' =>
                            $this->codigoErrorValidacion(
                                $campo
                            ),

                        'datos_recibidos' =>
                            $this->datosRecibidos(
                                $registro
                            ),

                        'detalle_tecnico' =>
                            json_encode(
                                [
                                    'campo' =>
                                        $campo,

                                    'id_importacion' =>
                                        $registro
                                            ->id_importacion,
                                ],
                                JSON_UNESCAPED_UNICODE
                                | JSON_UNESCAPED_SLASHES
                                | JSON_THROW_ON_ERROR
                            ),
                    ]
                );
            }
        }
    }

    /**
     * Registra una excepción inesperada
     * ocurrida en el procesador.
     */
    private function registrarExcepcion(
        SafCapacitacionImportacion $registro,
        SincronizacionSaf $ejecucion,
        Throwable $exception
    ): void {
        $this->auditoria
            ->registrarCapacitacionConError(
                $ejecucion
            );

        $this->auditoria->registrarExcepcion(
            sincronizacion:
                $ejecucion,

            exception:
                $exception,

            tipoRegistro:
                SincronizacionSafError::TIPO_REGISTRO_CAPACITACION,

            tipoOperacion:
                SincronizacionSafError::OPERACION_PROCESAR,

            datosRecibidos:
                $this->datosRecibidos(
                    $registro
                ),

            idRegistroExterno:
                $this->identificadorExterno(
                    $registro
                ),

            idRegistroLocal:
                $registro->id_importacion
        );
    }

    /**
     * Construye el identificador externo
     * de la capacitación para auditoría.
     *
     * Formato:
     *
     * id_instructor:codigo_evento
     */
    private function identificadorExterno(
        SafCapacitacionImportacion $registro
    ): string {
        $codigo = trim(
            (string) $registro->codigo_evento
        );

        if ($codigo === '') {
            $codigo = 'SIN_CODIGO';
        }

        return sprintf(
            '%s:%s',
            $registro->id_instructor,
            $codigo
        );
    }

    /**
     * Construye el código técnico de un
     * error de validación.
     */
    private function codigoErrorValidacion(
        string $campo
    ): string {
        $campoNormalizado = Str::of(
            $campo
        )
            ->upper()
            ->replace('.', '_')
            ->replace('-', '_')
            ->toString();

        return mb_substr(
            'VALIDACION_' . $campoNormalizado,
            0,
            100
        );
    }

    /**
     * Obtiene exclusivamente los datos de negocio
     * recibidos desde SAF.
     *
     * Los campos técnicos de procesamiento no se
     * mezclan con el contrato de SAF.
     */
    private function datosRecibidos(
        SafCapacitacionImportacion $registro
    ): array {
        return [
            'id_importacion' =>
                $registro->id_importacion,

            'id_instructor' =>
                $registro->id_instructor,

            'programa_curso_id' =>
                $registro->programa_curso_id,

            'codigo_evento' =>
                $registro->codigo_evento,

            'curso_nombre' =>
                $registro->curso_nombre,

            'fecha_inicio' =>
                $registro->fecha_inicio
                    ?->format('Y-m-d'),

            'fecha_fin' =>
                $registro->fecha_fin
                    ?->format('Y-m-d'),

            'estado_curso_nombre' =>
                $registro->estado_curso_nombre,

            'no_horas_real' =>
                $registro->no_horas_real,

            'modalidad' =>
                $registro->modalidad,

            'tipo_evento_nombre' =>
                $registro->tipo_evento_nombre,

            'cliente' =>
                $registro->cliente,

            'encuesta_id' =>
                $registro->encuesta_id,

            'encuesta_nombre' =>
                $registro->encuesta_nombre,

            'promedio_encuesta' =>
                $registro->promedio_encuesta,

            'fecha_evaluacion' =>
                $registro->fecha_evaluacion
                    ?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Marca el registro como procesado.
     */
    private function marcarProcesado(
        SafCapacitacionImportacion $registro,
        string $resultado,
        ?int $idRegistroLocal
    ): void {
        $registro->forceFill([
            'estado' =>
                SafCapacitacionImportacion::ESTADO_PROCESADO,

            'resultado_procesamiento' =>
                $resultado,

            'mensaje_error' =>
                null,

            'fecha_procesamiento' =>
                now(),

            'id_registro_local' =>
                $idRegistroLocal,
        ])->save();
    }

    /**
     * Marca el registro con error.
     */
    private function marcarError(
        SafCapacitacionImportacion $registro,
        string $mensaje
    ): void {
        $registro->forceFill([
            'estado' =>
                SafCapacitacionImportacion::ESTADO_ERROR,

            'resultado_procesamiento' =>
                SafCapacitacionSyncService::RESULTADO_ERROR,

            'mensaje_error' =>
                mb_substr(
                    $mensaje,
                    0,
                    65535
                ),

            'fecha_procesamiento' =>
                now(),

            'id_registro_local' =>
                null,
        ])->save();
    }

    /**
     * Obtiene el primer mensaje generado
     * por una ValidationException.
     */
    private function mensajeValidacion(
        ValidationException $exception
    ): string {
        $mensaje = collect(
            $exception->errors()
        )
            ->flatten()
            ->first();

        return is_string($mensaje)
            ? $mensaje
            : $exception->getMessage();
    }
}