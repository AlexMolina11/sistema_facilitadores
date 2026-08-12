<?php

namespace Tests\Unit\Modules\Fac\Services\Saf;

use App\Modules\Fac\Models\SafInstructorImportacion;
use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Services\Saf\SafAuditService;
use App\Modules\Fac\Services\Saf\SafInstructorImportacionProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SafInstructorImportacionProcessorTest extends TestCase
{
    use RefreshDatabase;

    private SafAuditService $auditoria;

    private SafInstructorImportacionProcessor $processor;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'saf.audit.enabled' => true,
            'saf.audit.store_error_payload' => true,
            'saf.audit.store_exception_details' => true,
            'saf.entity_id' => 1,
            'saf.hash.algorithm' => 'sha256',

            'saf.sensitive_fields' => [
                'password',
                'token',
                'secret',
            ],
        ]);

        $this->auditoria = app(
            SafAuditService::class
        );

        $this->processor = app(
            SafInstructorImportacionProcessor::class
        );

        /*
        |--------------------------------------------------------------------------
        | Catálogo de tipos de documento
        |--------------------------------------------------------------------------
        |
        | RefreshDatabase deja la base limpia.
        |
        | SafInstructorSyncService necesita estos registros para almacenar
        | correctamente los documentos provenientes de SAF.
        |
        | Mapeo:
        |
        | SAF 2 -> NIT        -> Facilitadores 1
        | SAF 4 -> Pasaporte  -> Facilitadores 4
        | SAF 5 -> Licencia   -> Facilitadores 6
        | SAF 7 -> DUI        -> Facilitadores 2
        |
        */
        DB::table(
            'tbl_tipo_documento'
        )->insertOrIgnore([
            [
                'id_tipo_documento' => 1,
                'nombre' => 'NIT',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_tipo_documento' => 2,
                'nombre' => 'DUI',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_tipo_documento' => 4,
                'nombre' => 'PASAPORTE',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_tipo_documento' => 6,
                'nombre' => 'LICENCIA DE CONDUCIR',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function test_it_processes_a_pending_instructor(): void
    {
        $registro = SafInstructorImportacion::query()->create([
            'id_instructor' => 5001,
            'id_entidad' => 1,

            'nombres' => 'Carlos',
            'apellidos' => 'Ramírez',

            /*
             * SAF:
             * 7 = DUI
             */
            'tipo_identificacion' => 7,

            'numero_identificacion' =>
                '01234567-8',

            'correo_saf' =>
                'carlos.ramirez@example.com',

            'activo' => true,

            'estado' =>
                SafInstructorImportacion::ESTADO_PENDIENTE,

            'intentos' => 0,

            'fecha_recepcion' => now(),
        ]);

        $ejecucion = $this->crearEjecucion(
            1
        );

        $resumen = $this->processor->procesar(
            $ejecucion
        );

        /*
        |--------------------------------------------------------------------------
        | Resumen
        |--------------------------------------------------------------------------
        */
        $this->assertSame(
            [
                'detectados' => 1,
                'procesados' => 1,
                'exitosos' => 1,
                'con_error' => 0,
            ],
            $resumen
        );

        /*
        |--------------------------------------------------------------------------
        | Registro staging
        |--------------------------------------------------------------------------
        */
        $registro->refresh();

        $this->assertSame(
            SafInstructorImportacion::ESTADO_PROCESADO,
            $registro->estado
        );

        $this->assertSame(
            'CREADO',
            $registro->resultado_procesamiento
        );

        $this->assertSame(
            1,
            $registro->intentos
        );

        $this->assertSame(
            $ejecucion->getKey(),
            $registro->id_sincronizacion
        );

        $this->assertNotNull(
            $registro->fecha_procesamiento
        );

        $this->assertNull(
            $registro->mensaje_error
        );

        $this->assertNotNull(
            $registro->id_registro_local
        );

        /*
        |--------------------------------------------------------------------------
        | Consultor
        |--------------------------------------------------------------------------
        |
        | El documento SAF ya NO se almacena en:
        |
        | tbl_consultor.numero_identificacion
        |
        | Debe permanecer NULL.
        |
        */
        $this->assertDatabaseHas(
            'tbl_consultor',
            [
                'id_instructor' => 5001,
                'id_entidad' => 1,
                'nombres' => 'Carlos',
                'apellidos' => 'Ramírez',

                'numero_identificacion' =>
                    null,

                'activo' => true,
                'vigente' => true,
                'origen_registro' => 'SAF',
            ]
        );

        $consultor = DB::table(
            'tbl_consultor'
        )
            ->where(
                'id_instructor',
                5001
            )
            ->first();

        $this->assertNotNull(
            $consultor
        );

        $this->assertSame(
            (int) $consultor->id_consultor,
            (int) $registro->id_registro_local
        );

        /*
        |--------------------------------------------------------------------------
        | Documento
        |--------------------------------------------------------------------------
        |
        | SAF 7 = DUI
        | Facilitadores 2 = DUI
        |
        */
        $this->assertDatabaseHas(
            'tbl_consultor_documento',
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'id_tipo_documento' =>
                    2,

                'numero' =>
                    '01234567-8',

                'activo' =>
                    true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Correo SAF
        |--------------------------------------------------------------------------
        |
        | Debe almacenarse como correo principal.
        |
        */
        $this->assertDatabaseHas(
            'tbl_consultor_email',
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'email' =>
                    'carlos.ramirez@example.com',

                'principal' =>
                    true,

                'activo' =>
                    true,
            ]
        );
    }

    public function test_it_does_not_process_a_completed_record(): void
    {
        SafInstructorImportacion::query()->create([
            'id_instructor' => 5002,
            'id_entidad' => 1,

            'nombres' => 'Ana',
            'apellidos' => 'Martínez',

            'tipo_identificacion' =>
                null,

            'numero_identificacion' =>
                null,

            'correo_saf' =>
                null,

            'activo' =>
                true,

            'estado' =>
                SafInstructorImportacion::ESTADO_PROCESADO,

            'resultado_procesamiento' =>
                'CREADO',

            'intentos' =>
                1,

            'fecha_recepcion' =>
                now(),

            'fecha_procesamiento' =>
                now(),
        ]);

        $ejecucion = $this->crearEjecucion(
            0
        );

        $resumen = $this->processor->procesar(
            $ejecucion
        );

        $this->assertSame(
            [
                'detectados' => 0,
                'procesados' => 0,
                'exitosos' => 0,
                'con_error' => 0,
            ],
            $resumen
        );

        $this->assertDatabaseMissing(
            'tbl_consultor',
            [
                'id_instructor' => 5002,
            ]
        );
    }

    public function test_it_marks_an_invalid_instructor_as_error(): void
    {
        $registro =
            SafInstructorImportacion::query()
                ->create([
                    'id_instructor' =>
                        5003,

                    /*
                     * La entidad configurada es 1.
                     * Utilizamos 99 para provocar
                     * intencionalmente el error.
                     */
                    'id_entidad' =>
                        99,

                    'nombres' =>
                        'Instructor',

                    'apellidos' =>
                        'Entidad incorrecta',

                    'tipo_identificacion' =>
                        null,

                    'numero_identificacion' =>
                        null,

                    'correo_saf' =>
                        null,

                    'activo' =>
                        true,

                    'estado' =>
                        SafInstructorImportacion::ESTADO_PENDIENTE,

                    'intentos' =>
                        0,

                    'fecha_recepcion' =>
                        now(),
                ]);

        $ejecucion = $this->crearEjecucion(
            1
        );

        $resumen = $this->processor->procesar(
            $ejecucion
        );

        $this->assertSame(
            1,
            $resumen['detectados']
        );

        $this->assertSame(
            1,
            $resumen['procesados']
        );

        $this->assertSame(
            0,
            $resumen['exitosos']
        );

        $this->assertSame(
            1,
            $resumen['con_error']
        );

        $registro->refresh();

        $this->assertSame(
            SafInstructorImportacion::ESTADO_ERROR,
            $registro->estado
        );

        $this->assertSame(
            1,
            $registro->intentos
        );

        $this->assertNotNull(
            $registro->mensaje_error
        );

        $this->assertNotNull(
            $registro->fecha_procesamiento
        );

        $this->assertSame(
            $ejecucion->getKey(),
            $registro->id_sincronizacion
        );

        $this->assertNull(
            $registro->id_registro_local
        );

        $this->assertDatabaseMissing(
            'tbl_consultor',
            [
                'id_instructor' => 5003,
            ]
        );

        /*
         * La bitácora debe registrar
         * la entidad incorrecta.
         */
        $this->assertDatabaseHas(
            'tbl_sincronizacion_saf_error',
            [
                'codigo_error' =>
                    'ENTIDAD_NO_PERMITIDA',
            ]
        );
    }

    public function test_it_respects_the_processing_limit(): void
    {
        foreach (range(1, 3) as $numero) {
            SafInstructorImportacion::query()
                ->create([
                    'id_instructor' =>
                        6000 + $numero,

                    'id_entidad' =>
                        1,

                    'nombres' =>
                        'Instructor',

                    'apellidos' =>
                        'Número ' . $numero,

                    /*
                     * Documento y correo pueden ser NULL.
                     *
                     * Un instructor sigue siendo válido
                     * mientras los datos obligatorios
                     * del contrato estén presentes.
                     */
                    'tipo_identificacion' =>
                        null,

                    'numero_identificacion' =>
                        null,

                    'correo_saf' =>
                        null,

                    'activo' =>
                        true,

                    'estado' =>
                        SafInstructorImportacion::ESTADO_PENDIENTE,

                    'intentos' =>
                        0,

                    'fecha_recepcion' =>
                        now(),
                ]);
        }

        $ejecucion = $this->crearEjecucion(
            2
        );

        $resumen = $this->processor->procesar(
            ejecucion:
                $ejecucion,

            limite:
                2
        );

        $this->assertSame(
            2,
            $resumen['detectados']
        );

        $this->assertSame(
            2,
            $resumen['procesados']
        );

        $this->assertSame(
            2,
            $resumen['exitosos']
        );

        $this->assertSame(
            0,
            $resumen['con_error']
        );

        $this->assertSame(
            2,
            SafInstructorImportacion::query()
                ->where(
                    'estado',
                    SafInstructorImportacion::ESTADO_PROCESADO
                )
                ->count()
        );

        $this->assertSame(
            1,
            SafInstructorImportacion::query()
                ->where(
                    'estado',
                    SafInstructorImportacion::ESTADO_PENDIENTE
                )
                ->count()
        );
    }

    private function crearEjecucion(
        int $total
    ): SincronizacionSaf {
        $ejecucion =
            $this->auditoria->iniciar(
                SincronizacionSaf::TIPO_MANUAL
            );

        return $this->auditoria
            ->establecerTotal(
                $ejecucion,
                $total
            );
    }
}