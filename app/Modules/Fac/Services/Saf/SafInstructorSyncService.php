<?php

namespace App\Modules\Fac\Services\Saf;

use App\Modules\Fac\Data\Saf\InstructorSafData;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\ConsultorDocumento;
use App\Modules\Fac\Models\ConsultorEmail;
use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Models\SincronizacionSafError;
use Illuminate\Support\Facades\DB;
use Throwable;

class SafInstructorSyncService
{
    public const RESULTADO_CREADO = 'CREADO';

    public const RESULTADO_ACTUALIZADO = 'ACTUALIZADO';

    public const RESULTADO_SIN_CAMBIOS = 'SIN_CAMBIOS';

    public const RESULTADO_ERROR = 'ERROR';

    public const RESULTADO_OMITIDO = 'OMITIDO';

    /*
    |--------------------------------------------------------------------------
    | IDs de tipos de documento en Facilitadores
    |--------------------------------------------------------------------------
    */
    private const DOCUMENTO_NIT = 1;
    private const DOCUMENTO_DUI = 2;
    private const DOCUMENTO_PASAPORTE = 4;
    private const DOCUMENTO_LICENCIA_CONDUCIR = 6;

    public function __construct(
        private readonly SafAuditService $auditoria
    ) {}

    /**
     * Sincroniza un instructor recibido desde SAF.
     *
     * La operación completa se ejecuta dentro de una transacción:
     *
     * - tbl_consultor
     * - tbl_consultor_documento
     * - tbl_consultor_email
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
            $resultado = DB::transaction(
                function () use ($instructor): array {
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
                }
            );

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
        $datos = $this->datosPersistiblesConsultor(
            $instructor
        );

        $consultor = new Consultor();

        $consultor->forceFill(
            array_merge(
                $datos,
                [
                    'origen_registro' =>
                        Consultor::ORIGEN_SAF,

                    'fecha_ultima_sincronizacion_saf' =>
                        now(),

                    /*
                     * El hash representa ahora TODO el contrato
                     * recibido desde SAF, incluyendo documento
                     * y correo.
                     */
                    'hash_datos_saf' =>
                        $instructor->hash(),

                    'usuario_crea' =>
                        $this->resolverUsuarioId(),

                    'usuario_mod' =>
                        null,

                    'usuario_elim' =>
                        null,
                ]
            )
        );

        $consultor->save();

        /*
         * El documento ya NO se guarda directamente
         * en tbl_consultor.
         */
        $this->sincronizarDocumento(
            $consultor,
            $instructor
        );

        /*
         * El correo SAF se registra como correo principal.
         */
        $this->sincronizarCorreo(
            $consultor,
            $instructor
        );

        return [
            'resultado' =>
                self::RESULTADO_CREADO,

            'consultor' =>
                $consultor->fresh(),

            'mensaje' =>
                'El instructor fue creado como consultor.',
        ];
    }

    /**
     * Actualiza un consultor previamente relacionado con SAF.
     */
    private function actualizarConsultor(
        Consultor $consultor,
        InstructorSafData $instructor
    ): array {
        $datos = $this->datosPersistiblesConsultor(
            $instructor
        );

        $nuevoHash = $instructor->hash();

        $estabaEliminado =
            $consultor->trashed();

        /*
         * El comportamiento histórico del sistema restaura
         * un consultor SAF si vuelve a ser recibido desde SAF.
         */
        if ($estabaEliminado) {
            $consultor->restore();
        }

        /*
         * Aunque el hash sea igual, verificamos documento
         * y correo.
         *
         * Esto permite reparar relaciones que hayan sido
         * eliminadas o modificadas internamente.
         */
        $documentoModificado =
            $this->sincronizarDocumento(
                $consultor,
                $instructor
            );

        $correoModificado =
            $this->sincronizarCorreo(
                $consultor,
                $instructor
            );

        $datosSafCambiaron =
            $consultor->hash_datos_saf !== $nuevoHash;

        if (
            ! $datosSafCambiaron
            && ! $documentoModificado
            && ! $correoModificado
            && ! $estabaEliminado
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

        $consultor->forceFill(
            array_merge(
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

                    'usuario_elim' =>
                        null,
                ]
            )
        );

        $consultor->save();

        return [
            'resultado' =>
                self::RESULTADO_ACTUALIZADO,

            'consultor' =>
                $consultor->fresh(),

            'mensaje' =>
                'El instructor fue actualizado.',
        ];
    }

    /**
     * Devuelve exclusivamente los campos pertenecientes
     * a tbl_consultor.
     *
     * IMPORTANTE:
     *
     * tipo_identificacion y numero_identificacion
     * deliberadamente NO se guardan aquí.
     */
    private function datosPersistiblesConsultor(
        InstructorSafData $instructor
    ): array {
        $activo =
            $instructor->activo ?? true;

        return [
            'id_instructor' =>
                $instructor->idInstructor,

            'id_entidad' =>
                $instructor->idEntidad,

            'nombres' =>
                $instructor->nombres,

            'apellidos' =>
                $instructor->apellidos,

            'activo' =>
                $activo,

            'vigente' =>
                $activo,
        ];
    }

    /**
     * Sincroniza el documento enviado por SAF.
     *
     * Retorna true si fue necesario crear, restaurar
     * o actualizar información.
     */
    private function sincronizarDocumento(
        Consultor $consultor,
        InstructorSafData $instructor
    ): bool {
        if (! $instructor->tieneDocumento()) {
            return false;
        }

        $idTipoDocumento =
            $this->mapearTipoDocumento(
                $instructor->tipoIdentificacion
            );

        $numero = trim(
            (string) $instructor->numeroIdentificacion
        );

        /*
         * Primero buscamos coincidencia exacta.
         *
         * Esto evita crear duplicados si el documento
         * ya estaba registrado previamente.
         */
        $documento = ConsultorDocumento::withTrashed()
            ->where(
                'id_consultor',
                $consultor->id_consultor
            )
            ->where(
                'id_tipo_documento',
                $idTipoDocumento
            )
            ->where(
                'numero',
                $numero
            )
            ->lockForUpdate()
            ->first();

        if ($documento !== null) {
            $modificado = false;

            if ($documento->trashed()) {
                $documento->restore();

                $modificado = true;
            }

            if (! $documento->activo) {
                $documento->activo = true;

                $modificado = true;
            }

            if ($modificado) {
                $documento->usuario_mod =
                    $this->resolverUsuarioId();

                $documento->usuario_elim =
                    null;

                $documento->save();
            }

            return $modificado;
        }

        /*
         * Si no encontramos el número exacto, buscamos
         * un documento existente del mismo tipo.
         *
         * Esto permite actualizar, por ejemplo, un DUI
         * cuyo número haya sido corregido por SAF.
         */
        $documento = ConsultorDocumento::withTrashed()
            ->where(
                'id_consultor',
                $consultor->id_consultor
            )
            ->where(
                'id_tipo_documento',
                $idTipoDocumento
            )
            ->orderByDesc('activo')
            ->orderByDesc('id_documento')
            ->lockForUpdate()
            ->first();

        if ($documento !== null) {
            if ($documento->trashed()) {
                $documento->restore();
            }

            $documento->forceFill([
                'numero' =>
                    $numero,

                'activo' =>
                    true,

                'usuario_mod' =>
                    $this->resolverUsuarioId(),

                'usuario_elim' =>
                    null,
            ])->save();

            return true;
        }

        $documento =
            new ConsultorDocumento();

        $documento->forceFill([
            'id_consultor' =>
                $consultor->id_consultor,

            'id_tipo_documento' =>
                $idTipoDocumento,

            'numero' =>
                $numero,

            'actividad_giro' =>
                null,

            'url_archivo' =>
                null,

            'activo' =>
                true,

            'usuario_crea' =>
                $this->resolverUsuarioId(),

            'usuario_mod' =>
                null,

            'usuario_elim' =>
                null,
        ]);

        $documento->save();

        return true;
    }

    /**
     * Sincroniza correo_saf como correo principal.
     *
     * Si existe otro correo principal del consultor,
     * se conserva como correo secundario.
     *
     * Retorna true cuando hubo cambios.
     */
    private function sincronizarCorreo(
        Consultor $consultor,
        InstructorSafData $instructor
    ): bool {
        if (! $instructor->tieneCorreoSaf()) {
            return false;
        }

        $correo = strtolower(
            trim(
                (string) $instructor->correoSaf
            )
        );

        $modificado = false;

        /*
         * Bloqueamos los correos activos del consultor
         * antes de modificar el principal.
         */
        $correosActuales =
            ConsultorEmail::withTrashed()
                ->where(
                    'id_consultor',
                    $consultor->id_consultor
                )
                ->lockForUpdate()
                ->get();

        $correoSaf = $correosActuales
            ->first(
                fn (ConsultorEmail $email): bool =>
                    strtolower(
                        trim($email->email)
                    ) === $correo
            );

        /*
         * Todo correo diferente al recibido desde SAF
         * deja de ser principal, pero NO se elimina ni
         * se desactiva.
         */
        foreach ($correosActuales as $email) {
            if (
                $correoSaf !== null
                && $email->id_email ===
                    $correoSaf->id_email
            ) {
                continue;
            }

            if (
                ! $email->trashed()
                && $email->principal
            ) {
                $email->forceFill([
                    'principal' =>
                        false,

                    'usuario_mod' =>
                        $this->resolverUsuarioId(),
                ])->save();

                $modificado = true;
            }
        }

        /*
         * El correo ya existe.
         */
        if ($correoSaf !== null) {
            if ($correoSaf->trashed()) {
                $correoSaf->restore();

                $modificado = true;
            }

            if (
                ! $correoSaf->principal
                || ! $correoSaf->activo
                || $correoSaf->email !== $correo
            ) {
                $correoSaf->forceFill([
                    'email' =>
                        $correo,

                    'principal' =>
                        true,

                    'activo' =>
                        true,

                    'usuario_mod' =>
                        $this->resolverUsuarioId(),

                    'usuario_elim' =>
                        null,
                ])->save();

                $modificado = true;
            }

            return $modificado;
        }

        /*
         * El correo no existe todavía.
         */
        $nuevoCorreo =
            new ConsultorEmail();

        $nuevoCorreo->forceFill([
            'id_consultor' =>
                $consultor->id_consultor,

            'email' =>
                $correo,

            'principal' =>
                true,

            'activo' =>
                true,

            'usuario_crea' =>
                $this->resolverUsuarioId(),

            'usuario_mod' =>
                null,

            'usuario_elim' =>
                null,
        ]);

        $nuevoCorreo->save();

        return true;
    }

    /**
     * Traduce el catálogo de SAF al catálogo
     * tbl_tipo_documento de Facilitadores.
     *
     * SAF:
     * 2 = NIT
     * 4 = Pasaporte
     * 5 = Licencia de conducir
     * 7 = DUI
     *
     * Facilitadores:
     * 1 = NIT
     * 2 = DUI
     * 4 = Pasaporte
     * 6 = Licencia de conducir
     */
    private function mapearTipoDocumento(
        ?int $tipoSaf
    ): int {
        return match ($tipoSaf) {
            InstructorSafData::TIPO_NIT =>
                self::DOCUMENTO_NIT,

            InstructorSafData::TIPO_PASAPORTE =>
                self::DOCUMENTO_PASAPORTE,

            InstructorSafData::TIPO_LICENCIA_CONDUCIR =>
                self::DOCUMENTO_LICENCIA_CONDUCIR,

            InstructorSafData::TIPO_DUI =>
                self::DOCUMENTO_DUI,

            default =>
                throw new \InvalidArgumentException(
                    sprintf(
                        'No existe equivalencia en Facilitadores para el tipo de identificación SAF: %s.',
                        $tipoSaf === null
                            ? 'NULL'
                            : (string) $tipoSaf
                    )
                ),
        };
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
     * Registra un instructor cuya entidad
     * no está permitida.
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
            sincronizacion:
                $sincronizacion,

            tipoRegistro:
                SincronizacionSafError::TIPO_REGISTRO_CONSULTOR,

            tipoOperacion:
                SincronizacionSafError::OPERACION_VALIDAR,

            mensaje:
                $mensaje,

            opciones: [
                'id_registro_externo' =>
                    $instructor->externalId(),

                'codigo_error' =>
                    'ENTIDAD_NO_PERMITIDA',

                'datos_recibidos' =>
                    $instructor->toArray(),
            ]
        );

        $this->auditoria
            ->registrarConsultorConError(
                $sincronizacion
            );

        return [
            'resultado' =>
                self::RESULTADO_OMITIDO,

            'consultor' =>
                null,

            'mensaje' =>
                $mensaje,
        ];
    }

    /**
     * Registra un fallo ocurrido durante
     * la creación o actualización.
     */
    private function registrarFallo(
        InstructorSafData $instructor,
        SincronizacionSaf $sincronizacion,
        Throwable $exception
    ): array {
        $this->auditoria->registrarExcepcion(
            sincronizacion:
                $sincronizacion,

            exception:
                $exception,

            tipoRegistro:
                SincronizacionSafError::TIPO_REGISTRO_CONSULTOR,

            tipoOperacion:
                SincronizacionSafError::OPERACION_PROCESAR,

            datosRecibidos:
                $instructor->toArray(),

            idRegistroExterno:
                $instructor->externalId()
        );

        $this->auditoria
            ->registrarConsultorConError(
                $sincronizacion
            );

        return [
            'resultado' =>
                self::RESULTADO_ERROR,

            'consultor' =>
                null,

            'mensaje' =>
                $exception->getMessage(),
        ];
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