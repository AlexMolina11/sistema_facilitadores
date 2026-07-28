<?php

namespace App\Modules\Fac\Services\Saf;

use App\Modules\Fac\Data\Saf\InstructorSafData;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Models\SincronizacionSafError;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class SafInstructorSyncService
{
    public const RESULTADO_CREADO = 'CREADO';

    public const RESULTADO_ACTUALIZADO = 'ACTUALIZADO';

    public const RESULTADO_SIN_CAMBIOS = 'SIN_CAMBIOS';

    public const RESULTADO_ERROR = 'ERROR';

    public const RESULTADO_OMITIDO = 'OMITIDO';

    public function __construct(
        private readonly SafAuditService $auditoria
    ) {
    }

    /**
     * Sincroniza un instructor recibido desde SAF.
     *
     * @return array{
     *     resultado: string,
     *     consultor: Consultor|null,
     *     mensaje: string|null
     * }
     */
    public function sincronizar(
        InstructorSafData $instructor,
        SincronizacionSaf $sincronizacion
    ): array {
        if (! $instructor->perteneceAEntidadConfigurada()) {
            return $this->registrarEntidadNoPermitida(
                $instructor,
                $sincronizacion
            );
        }

        try {
            $resultado = DB::transaction(function () use (
                $instructor
            ): array {
                $consultor = Consultor::withTrashed()
                    ->where(
                        'id_instructor',
                        $instructor->idInstructor
                    )
                    ->lockForUpdate()
                    ->first();

                if ($consultor === null) {
                    return $this->crearConsultor(
                        $instructor
                    );
                }

                return $this->actualizarConsultor(
                    $consultor,
                    $instructor
                );
            });

            $this->registrarResultadoExitoso(
                $sincronizacion,
                $resultado['resultado']
            );

            return $resultado;
        } catch (Throwable $exception) {
            return $this->registrarFallo(
                $instructor,
                $sincronizacion,
                $exception
            );
        }
    }

    /**
     * Sincroniza varios instructores dentro de una ejecución.
     *
     * @param iterable<InstructorSafData> $instructores
     *
     * @return array{
     *     recibidos: int,
     *     creados: int,
     *     actualizados: int,
     *     sin_cambios: int,
     *     omitidos: int,
     *     errores: int
     * }
     */
    public function sincronizarVarios(
        iterable $instructores,
        SincronizacionSaf $sincronizacion
    ): array {
        $resumen = [
            'recibidos' => 0,
            'creados' => 0,
            'actualizados' => 0,
            'sin_cambios' => 0,
            'omitidos' => 0,
            'errores' => 0,
        ];

        foreach ($instructores as $instructor) {
            $resumen['recibidos']++;

            $resultado = $this->sincronizar(
                $instructor,
                $sincronizacion
            );

            match ($resultado['resultado']) {
                self::RESULTADO_CREADO =>
                    $resumen['creados']++,

                self::RESULTADO_ACTUALIZADO =>
                    $resumen['actualizados']++,

                self::RESULTADO_SIN_CAMBIOS =>
                    $resumen['sin_cambios']++,

                self::RESULTADO_OMITIDO =>
                    $resumen['omitidos']++,

                self::RESULTADO_ERROR =>
                    $resumen['errores']++,

                default => null,
            };
        }

        return $resumen;
    }

    /**
     * Crea un consultor nuevo con origen SAF.
     */
    private function crearConsultor(
        InstructorSafData $instructor
    ): array {
        $datos = $this->datosPersistibles(
            $instructor
        );

        $consultor = new Consultor();

        $consultor->forceFill(array_merge(
            $datos,
            [
                'origen_registro' =>
                    Consultor::ORIGEN_SAF,

                'fecha_ultima_sincronizacion_saf' =>
                    now(),

                'hash_datos_saf' =>
                    $this->generarHash($datos),

                'usuario_crea' =>
                    $this->resolverUsuarioId(),

                'usuario_mod' => null,
                'usuario_elim' => null,
            ]
        ));

        $consultor->save();

        return [
            'resultado' => self::RESULTADO_CREADO,
            'consultor' => $consultor->fresh(),
            'mensaje' => 'El instructor fue creado como consultor.',
        ];
    }

    /**
     * Actualiza un consultor previamente relacionado con SAF.
     */
    private function actualizarConsultor(
        Consultor $consultor,
        InstructorSafData $instructor
    ): array {
        $datos = $this->datosPersistibles(
            $instructor
        );

        $nuevoHash = $this->generarHash($datos);

        if (
            $consultor->hash_datos_saf === $nuevoHash
            && $consultor->deleted_at === null
        ) {
            $consultor->forceFill([
                'fecha_ultima_sincronizacion_saf' =>
                    now(),

                'usuario_mod' =>
                    $this->resolverUsuarioId(),
            ])->save();

            return [
                'resultado' =>
                    self::RESULTADO_SIN_CAMBIOS,

                'consultor' =>
                    $consultor->fresh(),

                'mensaje' =>
                    'El instructor no presentó cambios.',
            ];
        }

        if ($consultor->trashed()) {
            $consultor->restore();
        }

        $consultor->forceFill(array_merge(
            $datos,
            [
                'origen_registro' =>
                    Consultor::ORIGEN_SAF,

                'fecha_ultima_sincronizacion_saf' =>
                    now(),

                'hash_datos_saf' =>
                    $nuevoHash,

                'usuario_mod' =>
                    $this->resolverUsuarioId(),

                'usuario_elim' => null,
            ]
        ));

        $consultor->save();

        return [
            'resultado' => self::RESULTADO_ACTUALIZADO,
            'consultor' => $consultor->fresh(),
            'mensaje' => 'El instructor fue actualizado.',
        ];
    }

    /**
     * Convierte el DTO en campos existentes en tbl_consultor.
     */
    private function datosPersistibles(
        InstructorSafData $instructor
    ): array {
        $activo = $instructor->activo ?? true;

        return [
            'id_instructor' =>
                $instructor->idInstructor,

            'id_entidad' =>
                $instructor->idEntidad,

            'nombres' =>
                $instructor->nombres,

            'apellidos' =>
                $instructor->apellidos,

            'numero_identificacion' =>
                $instructor->dui,

            'tipo_identificacion' =>
                $instructor->dui !== null
                    ? 'DUI'
                    : null,

            'activo' =>
                $activo,

            'vigente' =>
                $activo,
        ];
    }

    /**
     * Registra el contador correspondiente al resultado.
     */
    private function registrarResultadoExitoso(
        SincronizacionSaf $sincronizacion,
        string $resultado
    ): void {
        match ($resultado) {
            self::RESULTADO_CREADO =>
                $this->auditoria
                    ->registrarConsultorCreado(
                        $sincronizacion
                    ),

            self::RESULTADO_ACTUALIZADO =>
                $this->auditoria
                    ->registrarConsultorActualizado(
                        $sincronizacion
                    ),

            self::RESULTADO_SIN_CAMBIOS =>
                $this->auditoria
                    ->registrarConsultorSinCambios(
                        $sincronizacion
                    ),

            default => null,
        };
    }

    /**
     * Registra un instructor cuya entidad no está permitida.
     */
    private function registrarEntidadNoPermitida(
        InstructorSafData $instructor,
        SincronizacionSaf $sincronizacion
    ): array {
        $mensaje = sprintf(
            'El instructor %d pertenece a la entidad %d, '
            . 'la cual no coincide con la entidad SAF configurada.',
            $instructor->idInstructor,
            $instructor->idEntidad
        );

        $this->auditoria->registrarError(
            sincronizacion: $sincronizacion,

            tipoRegistro:
                SincronizacionSafError::TIPO_REGISTRO_CONSULTOR,

            tipoOperacion:
                SincronizacionSafError::OPERACION_VALIDAR,

            mensaje: $mensaje,

            opciones: [
                'id_registro_externo' =>
                    $instructor->externalId(),

                'codigo_error' =>
                    'ENTIDAD_NO_PERMITIDA',

                'datos_recibidos' =>
                    $instructor->toArray(),
            ]
        );

        $this->auditoria->registrarConsultorConError(
            $sincronizacion
        );

        return [
            'resultado' => self::RESULTADO_OMITIDO,
            'consultor' => null,
            'mensaje' => $mensaje,
        ];
    }

    /**
     * Registra un fallo ocurrido al crear o actualizar.
     */
    private function registrarFallo(
        InstructorSafData $instructor,
        SincronizacionSaf $sincronizacion,
        Throwable $exception
    ): array {
        $this->auditoria->registrarExcepcion(
            sincronizacion: $sincronizacion,
            exception: $exception,

            tipoRegistro:
                SincronizacionSafError::TIPO_REGISTRO_CONSULTOR,

            tipoOperacion:
                SincronizacionSafError::OPERACION_PROCESAR,

            datosRecibidos:
                $instructor->toArray(),

            idRegistroExterno:
                $instructor->externalId()
        );

        $this->auditoria->registrarConsultorConError(
            $sincronizacion
        );

        return [
            'resultado' => self::RESULTADO_ERROR,
            'consultor' => null,
            'mensaje' => $exception->getMessage(),
        ];
    }

    /**
     * Genera el hash únicamente con los datos almacenados
     * directamente en tbl_consultor.
     */
    private function generarHash(array $datos): string
    {
        ksort($datos);

        $algoritmo = config(
            'saf.hash.algorithm',
            'sha256'
        );

        if (
            ! is_string($algoritmo)
            || ! in_array(
                $algoritmo,
                hash_algos(),
                true
            )
        ) {
            $algoritmo = 'sha256';
        }

        return hash(
            $algoritmo,
            json_encode(
                $datos,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
            )
        );
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