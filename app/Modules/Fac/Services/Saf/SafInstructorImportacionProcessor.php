<?php

namespace App\Modules\Fac\Services\Saf;

use App\Modules\Fac\Data\Saf\InstructorSafData;
use App\Modules\Fac\Models\SafInstructorImportacion;
use App\Modules\Fac\Models\SincronizacionSaf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class SafInstructorImportacionProcessor
{
    public function __construct(
        private readonly SafInstructorSyncService $sincronizacion
    ) {}

    /**
     * Procesa los instructores SAF pendientes.
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

    /**
     * Reserva un registro pendiente para esta ejecución.
     */
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
                    'estado' => SafInstructorImportacion::ESTADO_EN_PROCESO,

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
     * Convierte y sincroniza un registro de importación.
     */
    private function procesarRegistro(
        SafInstructorImportacion $registro,
        SincronizacionSaf $ejecucion
    ): bool {
        try {
            $instructor = InstructorSafData::fromArray([
                'id_instructor' => $registro->id_instructor,

                'id_entidad' => $registro->id_entidad,

                'nombres' => $registro->nombres,

                'apellidos' => $registro->apellidos,

                'dui' => $registro->dui,

                'activo' => $registro->activo,
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
                $this->marcarProcesado($registro);

                return true;
            }

            $this->marcarError(
                $registro,
                $resultado['mensaje']
                    ?? 'El instructor no pudo ser sincronizado.'
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
     * Marca el registro como procesado correctamente.
     */
    private function marcarProcesado(
        SafInstructorImportacion $registro
    ): void {
        $registro->forceFill([
            'estado' => SafInstructorImportacion::ESTADO_PROCESADO,

            'mensaje_error' => null,

            'fecha_procesamiento' => now(),
        ])->save();
    }

    /**
     * Marca el registro con error.
     */
    private function marcarError(
        SafInstructorImportacion $registro,
        string $mensaje
    ): void {
        $registro->forceFill([
            'estado' => SafInstructorImportacion::ESTADO_ERROR,

            'mensaje_error' => mb_substr($mensaje, 0, 65535),

            'fecha_procesamiento' => now(),
        ])->save();
    }

    /**
     * Obtiene el primer mensaje de una validación.
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
