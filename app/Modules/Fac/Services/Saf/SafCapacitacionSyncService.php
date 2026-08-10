<?php

namespace App\Modules\Fac\Services\Saf;

use App\Modules\Fac\Data\Saf\CapacitacionSafData;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\ConsultorCapacitacionFepade;
use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Models\SincronizacionSafError;
use Illuminate\Support\Facades\DB;
use Throwable;

class SafCapacitacionSyncService
{
    public const RESULTADO_CREADO = 'CREADO';

    public const RESULTADO_ACTUALIZADO = 'ACTUALIZADO';

    public const RESULTADO_SIN_CAMBIOS = 'SIN_CAMBIOS';

    /*
     * Se conserva temporalmente por compatibilidad con
     * SafCapacitacionImportacionProcessor del Bloque 3.
     *
     * El nuevo servicio ya NO devolverá este resultado.
     */
    public const RESULTADO_DESACTIVADO = 'DESACTIVADO';

    public const RESULTADO_ERROR = 'ERROR';

    public function __construct(
        private readonly SafAuditService $auditoria
    ) {}

    /**
     * Sincroniza una capacitación individual recibida desde SAF.
     *
     * @return array{
     *     resultado: string,
     *     capacitacion: object|null,
     *     mensaje: string
     * }
     */
    public function sincronizar(
        CapacitacionSafData $capacitacion,
        SincronizacionSaf $sincronizacion
    ): array {
        try {
            /*
             * La capacitación solamente puede sincronizarse
             * cuando ya existe el consultor asociado.
             */
            $consultor = Consultor::query()
                ->where(
                    'id_instructor',
                    $capacitacion->idInstructor
                )
                ->first();

            if ($consultor === null) {
                return $this->registrarConsultorNoEncontrado(
                    $capacitacion,
                    $sincronizacion
                );
            }

            $resultado = DB::transaction(
                function () use (
                    $capacitacion,
                    $consultor
                ): array {
                    /*
                     * Identidad funcional conservada:
                     *
                     * id_consultor + codigo_evento
                     */
                    $registro = DB::table(
                        'tbl_consultor_capacitacion_fepade'
                    )
                        ->where(
                            'id_consultor',
                            $consultor->id_consultor
                        )
                        ->where(
                            'codigo_evento',
                            $capacitacion->codigoEvento
                        )
                        ->lockForUpdate()
                        ->first();

                    if ($registro === null) {
                        return $this->crearCapacitacion(
                            $consultor,
                            $capacitacion
                        );
                    }

                    return $this->actualizarCapacitacion(
                        $registro,
                        $capacitacion
                    );
                }
            );

            $this->registrarResultadoExitoso(
                $sincronizacion,
                $resultado['resultado']
            );

            return $resultado;
        } catch (Throwable $exception) {
            $this->auditoria
                ->registrarCapacitacionConError(
                    $sincronizacion
                );

            $this->auditoria->registrarExcepcion(
                sincronizacion:
                    $sincronizacion,

                exception:
                    $exception,

                tipoRegistro:
                    SincronizacionSafError::TIPO_REGISTRO_CAPACITACION,

                tipoOperacion:
                    SincronizacionSafError::OPERACION_PROCESAR,

                datosRecibidos:
                    $capacitacion->toArray(),

                idRegistroExterno:
                    $capacitacion->externalId()
            );

            return [
                'resultado' =>
                    self::RESULTADO_ERROR,

                'capacitacion' =>
                    null,

                'mensaje' =>
                    $exception->getMessage(),
            ];
        }
    }

    /**
     * Crea una capacitación nueva.
     */
    private function crearCapacitacion(
        Consultor $consultor,
        CapacitacionSafData $capacitacion
    ): array {
        $ahora = now();

        $id = DB::table(
            'tbl_consultor_capacitacion_fepade'
        )->insertGetId(
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'programa_curso_id' =>
                    $capacitacion->programaCursoId,

                'codigo_evento' =>
                    $capacitacion->codigoEvento,

                'curso_nombre' =>
                    $capacitacion->cursoNombre,

                'fecha_inicio' =>
                    $capacitacion->fechaInicio
                        ?->format('Y-m-d'),

                'fecha_fin' =>
                    $capacitacion->fechaFin
                        ?->format('Y-m-d'),

                'estado_curso_nombre' =>
                    $capacitacion->estadoCursoNombre,

                'no_horas_real' =>
                    $capacitacion->noHorasReal,

                'modalidad' =>
                    $capacitacion->modalidad,

                'tipo_evento_nombre' =>
                    $capacitacion->tipoEventoNombre,

                'cliente' =>
                    $capacitacion->cliente,

                'encuesta_id' =>
                    $capacitacion->encuestaId,

                'encuesta_nombre' =>
                    $capacitacion->encuestaNombre,

                'promedio_encuesta' =>
                    $capacitacion->promedioEncuesta,

                'fecha_evaluacion' =>
                    $capacitacion->fechaEvaluacion
                        ?->format('Y-m-d H:i:s'),

                /*
                 |--------------------------------------------------------------------------
                 | Datos internos de Facilitadores
                 |--------------------------------------------------------------------------
                 */
                'fuente' =>
                    ConsultorCapacitacionFepade::FUENTE_SAF,

                'fecha_ultima_sincronizacion_saf' =>
                    $ahora,

                'hash_datos_saf' =>
                    $capacitacion->hash(),

                /*
                 * Una capacitación nueva se crea activa.
                 *
                 * A partir de aquí SAF NO controla este campo.
                 */
                'activo' =>
                    true,

                'usuario_crea' =>
                    $this->resolverUsuarioId(),

                'usuario_mod' =>
                    null,

                'usuario_elim' =>
                    null,

                'created_at' =>
                    $ahora,

                'updated_at' =>
                    $ahora,

                'deleted_at' =>
                    null,
            ],
            'id_capacitacion_fepade'
        );

        return [
            'resultado' =>
                self::RESULTADO_CREADO,

            'capacitacion' =>
                $this->buscarCapacitacion(
                    $id
                ),

            'mensaje' =>
                'La capacitación fue creada.',
        ];
    }

    /**
     * Actualiza una capacitación existente.
     *
     * IMPORTANTE:
     *
     * SAF NO modifica:
     *
     * - activo
     * - deleted_at
     * - usuario_elim
     *
     * Esos campos pertenecen al control interno
     * del Sistema de Facilitadores.
     */
    private function actualizarCapacitacion(
        object $registro,
        CapacitacionSafData $capacitacion
    ): array {
        $nuevoHash =
            $capacitacion->hash();

        $ahora =
            now();

        if (
            $registro->hash_datos_saf === $nuevoHash
        ) {
            DB::table(
                'tbl_consultor_capacitacion_fepade'
            )
                ->where(
                    'id_capacitacion_fepade',
                    $registro->id_capacitacion_fepade
                )
                ->update([
                    'fecha_ultima_sincronizacion_saf' =>
                        $ahora,

                    'updated_at' =>
                        $ahora,
                ]);

            return [
                'resultado' =>
                    self::RESULTADO_SIN_CAMBIOS,

                'capacitacion' =>
                    $this->buscarCapacitacion(
                        $registro->id_capacitacion_fepade
                    ),

                'mensaje' =>
                    'La capacitación no presentó cambios.',
            ];
        }

        DB::table(
            'tbl_consultor_capacitacion_fepade'
        )
            ->where(
                'id_capacitacion_fepade',
                $registro->id_capacitacion_fepade
            )
            ->update([
                'programa_curso_id' =>
                    $capacitacion->programaCursoId,

                'codigo_evento' =>
                    $capacitacion->codigoEvento,

                'curso_nombre' =>
                    $capacitacion->cursoNombre,

                'fecha_inicio' =>
                    $capacitacion->fechaInicio
                        ?->format('Y-m-d'),

                'fecha_fin' =>
                    $capacitacion->fechaFin
                        ?->format('Y-m-d'),

                'estado_curso_nombre' =>
                    $capacitacion->estadoCursoNombre,

                'no_horas_real' =>
                    $capacitacion->noHorasReal,

                'modalidad' =>
                    $capacitacion->modalidad,

                'tipo_evento_nombre' =>
                    $capacitacion->tipoEventoNombre,

                'cliente' =>
                    $capacitacion->cliente,

                'encuesta_id' =>
                    $capacitacion->encuestaId,

                'encuesta_nombre' =>
                    $capacitacion->encuestaNombre,

                'promedio_encuesta' =>
                    $capacitacion->promedioEncuesta,

                'fecha_evaluacion' =>
                    $capacitacion->fechaEvaluacion
                        ?->format('Y-m-d H:i:s'),

                'fuente' =>
                    ConsultorCapacitacionFepade::FUENTE_SAF,

                'fecha_ultima_sincronizacion_saf' =>
                    $ahora,

                'hash_datos_saf' =>
                    $nuevoHash,

                'usuario_mod' =>
                    $this->resolverUsuarioId(),

                'updated_at' =>
                    $ahora,

                /*
                 * Deliberadamente NO modificamos:
                 *
                 * activo
                 * deleted_at
                 * usuario_elim
                 */
            ]);

        return [
            'resultado' =>
                self::RESULTADO_ACTUALIZADO,

            'capacitacion' =>
                $this->buscarCapacitacion(
                    $registro->id_capacitacion_fepade
                ),

            'mensaje' =>
                'La capacitación fue actualizada.',
        ];
    }

    /**
     * Registra una capacitación cuyo instructor no existe.
     */
    private function registrarConsultorNoEncontrado(
        CapacitacionSafData $capacitacion,
        SincronizacionSaf $sincronizacion
    ): array {
        $mensaje = sprintf(
            'No existe un consultor relacionado con el id_instructor %d.',
            $capacitacion->idInstructor
        );

        $this->auditoria
            ->registrarCapacitacionConError(
                $sincronizacion
            );

        $this->auditoria->registrarError(
            sincronizacion:
                $sincronizacion,

            tipoRegistro:
                SincronizacionSafError::TIPO_REGISTRO_CAPACITACION,

            tipoOperacion:
                SincronizacionSafError::OPERACION_VALIDAR,

            mensaje:
                $mensaje,

            opciones: [
                'id_registro_externo' =>
                    $capacitacion->externalId(),

                'codigo_error' =>
                    'CONSULTOR_NO_ENCONTRADO',

                'datos_recibidos' =>
                    $capacitacion->toArray(),
            ]
        );

        return [
            'resultado' =>
                self::RESULTADO_ERROR,

            'capacitacion' =>
                null,

            'mensaje' =>
                $mensaje,
        ];
    }

    /**
     * Incrementa los contadores de auditoría.
     */
    private function registrarResultadoExitoso(
        SincronizacionSaf $sincronizacion,
        string $resultado
    ): void {
        match ($resultado) {
            self::RESULTADO_CREADO =>
                $this->auditoria
                    ->registrarCapacitacionCreada(
                        $sincronizacion
                    ),

            self::RESULTADO_ACTUALIZADO =>
                $this->auditoria
                    ->registrarCapacitacionActualizada(
                        $sincronizacion
                    ),

            self::RESULTADO_SIN_CAMBIOS =>
                $this->auditoria
                    ->registrarCapacitacionSinCambios(
                        $sincronizacion
                    ),

            /*
             * RESULTADO_DESACTIVADO ya no se genera.
             * La constante queda únicamente por compatibilidad
             * durante esta etapa de la migración.
             */

            default => null,
        };
    }

    /**
     * Consulta una capacitación por su llave primaria.
     */
    private function buscarCapacitacion(
        int $idCapacitacion
    ): ?object {
        return DB::table(
            'tbl_consultor_capacitacion_fepade'
        )
            ->where(
                'id_capacitacion_fepade',
                $idCapacitacion
            )
            ->first();
    }

    /**
     * Obtiene el usuario autenticado.
     */
    private function resolverUsuarioId(): ?int
    {
        $idUsuario = auth()->id();

        return $idUsuario !== null
            ? (int) $idUsuario
            : null;
    }
}