<?php

namespace App\Modules\Fac\Services\Saf;

use App\Modules\Fac\Data\Saf\CapacitacionSafData;
use App\Modules\Fac\Models\SafCapacitacionImportacion;
use App\Modules\Fac\Models\SincronizacionSaf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class SafCapacitacionImportacionProcessor
{
    public function __construct(
        private readonly SafCapacitacionSyncService $sincronizacion
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
                    'estado' => SafCapacitacionImportacion::ESTADO_EN_PROCESO,

                    'intentos' => (int) $registro->intentos + 1,

                    'mensaje_error' => null,

                    'fecha_procesamiento' => null,

                    'id_sincronizacion' => $ejecucion->getKey(),
                ])->save();

                return $registro->fresh();
            }
        );
    }

    /**
     * Convierte y sincroniza una capacitación.
     */
    private function procesarRegistro(
        SafCapacitacionImportacion $registro,
        SincronizacionSaf $ejecucion
    ): bool {
        try {
            $capacitacion =
                CapacitacionSafData::fromArray([
                    'id_instructor' => $registro->id_instructor,

                    'codigo_evento_externo' => $registro->codigo_evento_externo,

                    'nombre' => $registro->nombre,

                    'fecha_inicio' => $registro->fecha_inicio?->format('Y-m-d'),

                    'fecha_fin' => $registro->fecha_fin?->format('Y-m-d'),

                    'horas' => $registro->horas,

                    'activo' => $registro->activo,
                ]);

            $resultado =
                $this->sincronizacion->sincronizar(
                    $capacitacion,
                    $ejecucion
                );

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
                $this->marcarProcesado($registro);

                return true;
            }

            $this->marcarError(
                $registro,
                $resultado['mensaje']
                    ?? 'La capacitación no pudo ser sincronizada.'
            );

            return false;
        } catch (ValidationException $exception) {
            $this->marcarError(
                $registro,
                $this->mensajeValidacion($exception)
            );

            return false;
        } catch (Throwable $exception) {
            $this->marcarError(
                $registro,
                $exception->getMessage()
            );

            return false;
        }
    }

    /**
     * Marca el registro como procesado.
     */
    private function marcarProcesado(
        SafCapacitacionImportacion $registro
    ): void {
        $registro->forceFill([
            'estado' => SafCapacitacionImportacion::ESTADO_PROCESADO,

            'mensaje_error' => null,

            'fecha_procesamiento' => now(),
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
            'estado' => SafCapacitacionImportacion::ESTADO_ERROR,

            'mensaje_error' => mb_substr($mensaje, 0, 65535),

            'fecha_procesamiento' => now(),
        ])->save();
    }

    /**
     * Obtiene el primer mensaje de validación.
     */
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
