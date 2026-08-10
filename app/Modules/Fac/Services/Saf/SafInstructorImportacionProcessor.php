<?php

namespace App\Modules\Fac\Services\Saf;

use App\Modules\Fac\Data\Saf\InstructorSafData;
use App\Modules\Fac\Models\SafInstructorImportacion;
use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Models\SincronizacionSafError;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class SafInstructorImportacionProcessor
{
    public function __construct(
        private readonly SafInstructorSyncService $sincronizacion,
        private readonly SafAuditService $auditoria
    ) {}

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

        $consulta = SafInstructorImportacion::query()
            ->pendientes()
            ->orderBy('id_importacion');

        if ($limite !== null && $limite > 0) {
            $consulta->limit($limite);
        }

        $ids = $consulta->pluck('id_importacion');

        $resumen['detectados'] = $ids->count();

        foreach ($ids as $idImportacion) {
            $registro = $this->tomarRegistro(
                (int) $idImportacion,
                $ejecucion
            );

            if ($registro === null) {
                continue;
            }

            $resultado = $this->procesarRegistro(
                $registro,
                $ejecucion
            );

            $resumen['procesados']++;

            if ($resultado) {
                $resumen['exitosos']++;
            } else {
                $resumen['con_error']++;
            }
        }

        return $resumen;
    }

    private function tomarRegistro(
        int $idImportacion,
        SincronizacionSaf $ejecucion
    ): ?SafInstructorImportacion {
        return DB::transaction(
            function () use (
                $idImportacion,
                $ejecucion
            ): ?SafInstructorImportacion {
                $registro = SafInstructorImportacion::query()
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
                        SafInstructorImportacion::ESTADO_EN_PROCESO,

                    'resultado_procesamiento' => null,

                    'intentos' =>
                        (int) $registro->intentos + 1,

                    'mensaje_error' => null,

                    'fecha_procesamiento' => null,

                    'id_sincronizacion' =>
                        $ejecucion->getKey(),

                    'id_registro_local' => null,
                ])->save();

                return $registro->fresh();
            }
        );
    }

    private function procesarRegistro(
        SafInstructorImportacion $registro,
        SincronizacionSaf $ejecucion
    ): bool {
        try {
            $instructor = InstructorSafData::fromArray([
                'id_instructor' =>
                    $registro->id_instructor,

                'id_entidad' =>
                    $registro->id_entidad,

                'nombres' =>
                    $registro->nombres,

                'apellidos' =>
                    $registro->apellidos,

                'dui' =>
                    $registro->dui,

                'activo' =>
                    $registro->activo,
            ]);

            $resultado = $this->sincronizacion->sincronizar(
                $instructor,
                $ejecucion
            );

            if (
                in_array(
                    $resultado['resultado'],
                    [
                        SafInstructorSyncService::RESULTADO_CREADO,
                        SafInstructorSyncService::RESULTADO_ACTUALIZADO,
                        SafInstructorSyncService::RESULTADO_SIN_CAMBIOS,
                    ],
                    true
                )
            ) {
                $this->marcarProcesado(
                    registro: $registro,
                    resultado: $resultado['resultado'],
                    idRegistroLocal:
                        $resultado['consultor']?->getKey()
                );

                return true;
            }

            $this->marcarError(
                registro: $registro,

                mensaje:
                    $resultado['mensaje']
                    ?? 'El instructor no pudo ser sincronizado.',

                resultado:
                    $resultado['resultado']
                    ?? SafInstructorSyncService::RESULTADO_ERROR
            );

            return false;
        } catch (ValidationException $exception) {
            $mensaje = $this->mensajeValidacion(
                $exception
            );

            $this->marcarError(
                registro: $registro,
                mensaje: $mensaje,
                resultado:
                    SafInstructorSyncService::RESULTADO_ERROR
            );

            $this->registrarErroresValidacion(
                $registro,
                $ejecucion,
                $exception
            );

            return false;
        } catch (Throwable $exception) {
            $this->marcarError(
                registro: $registro,
                mensaje: $exception->getMessage(),
                resultado:
                    SafInstructorSyncService::RESULTADO_ERROR
            );

            $this->registrarExcepcion(
                $registro,
                $ejecucion,
                $exception
            );

            return false;
        }
    }

    private function registrarErroresValidacion(
        SafInstructorImportacion $registro,
        SincronizacionSaf $ejecucion,
        ValidationException $exception
    ): void {
        $this->auditoria->registrarConsultorConError(
            $ejecucion
        );

        foreach ($exception->errors() as $campo => $mensajes) {
            foreach ($mensajes as $mensaje) {
                $this->auditoria->registrarError(
                    sincronizacion: $ejecucion,

                    tipoRegistro:
                        SincronizacionSafError::TIPO_REGISTRO_CONSULTOR,

                    tipoOperacion:
                        SincronizacionSafError::OPERACION_VALIDAR,

                    mensaje: (string) $mensaje,

                    opciones: [
                        'id_registro_externo' =>
                            (string) $registro->id_instructor,

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
                                    'campo' => $campo,

                                    'id_importacion' =>
                                        $registro->id_importacion,
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

    private function registrarExcepcion(
        SafInstructorImportacion $registro,
        SincronizacionSaf $ejecucion,
        Throwable $exception
    ): void {
        $this->auditoria->registrarConsultorConError(
            $ejecucion
        );

        $this->auditoria->registrarExcepcion(
            sincronizacion: $ejecucion,

            exception: $exception,

            tipoRegistro:
                SincronizacionSafError::TIPO_REGISTRO_CONSULTOR,

            tipoOperacion:
                SincronizacionSafError::OPERACION_PROCESAR,

            datosRecibidos:
                $this->datosRecibidos($registro),

            idRegistroExterno:
                (string) $registro->id_instructor,

            idRegistroLocal:
                $registro->id_importacion
        );
    }

    private function codigoErrorValidacion(
        string $campo
    ): string {
        $campoNormalizado = Str::of($campo)
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

    private function datosRecibidos(
        SafInstructorImportacion $registro
    ): array {
        return [
            'id_importacion' =>
                $registro->id_importacion,

            'id_instructor' =>
                $registro->id_instructor,

            'id_entidad' =>
                $registro->id_entidad,

            'nombres' =>
                $registro->nombres,

            'apellidos' =>
                $registro->apellidos,

            'dui' =>
                $registro->dui,

            'activo' =>
                $registro->activo,
        ];
    }

    private function marcarProcesado(
        SafInstructorImportacion $registro,
        string $resultado,
        ?int $idRegistroLocal
    ): void {
        $registro->forceFill([
            'estado' =>
                SafInstructorImportacion::ESTADO_PROCESADO,

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

    private function marcarError(
        SafInstructorImportacion $registro,
        string $mensaje,
        string $resultado
    ): void {
        $registro->forceFill([
            'estado' =>
                SafInstructorImportacion::ESTADO_ERROR,

            'resultado_procesamiento' =>
                $resultado,

            'mensaje_error' =>
                mb_substr($mensaje, 0, 65535),

            'fecha_procesamiento' =>
                now(),

            'id_registro_local' =>
                null,
        ])->save();
    }

    private function mensajeValidacion(
        ValidationException $exception
    ): string {
        $mensaje = collect($exception->errors())
            ->flatten()
            ->first();

        return is_string($mensaje)
            ? $mensaje
            : $exception->getMessage();
    }
}