<?php

namespace Tests\Feature\Console;

use App\Modules\Fac\Models\SafCapacitacionImportacion;
use App\Modules\Fac\Models\SafInstructorImportacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProcesarImportacionesSafCommandTest extends TestCase
{
    use RefreshDatabase;

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

        /*
        |--------------------------------------------------------------------------
        | Catálogo requerido por la sincronización SAF
        |--------------------------------------------------------------------------
        |
        | RefreshDatabase ejecuta las migraciones, pero no los seeders.
        |
        | El procesamiento del instructor necesita tbl_tipo_documento
        | para poder transformar:
        |
        | SAF 2 -> NIT       -> Facilitadores 1
        | SAF 4 -> Pasaporte -> Facilitadores 4
        | SAF 5 -> Licencia  -> Facilitadores 6
        | SAF 7 -> DUI       -> Facilitadores 2
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

    public function test_it_processes_instructors_before_training_records(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Instructor pendiente
        |--------------------------------------------------------------------------
        */
        SafInstructorImportacion::query()->create([
            'id_instructor' => 11001,
            'id_entidad' => 1,

            'nombres' => 'Instructor',
            'apellidos' => 'Prueba comando',

            /*
             * SAF 7 = DUI
             */
            'tipo_identificacion' => 7,

            'numero_identificacion' =>
                '01111111-1',

            'correo_saf' =>
                'instructor.11001@example.com',

            'activo' =>
                true,

            'estado' =>
                SafInstructorImportacion::ESTADO_PENDIENTE,

            'intentos' =>
                0,

            'fecha_recepcion' =>
                now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. Capacitación del mismo instructor
        |--------------------------------------------------------------------------
        |
        | Esta prueba valida además el orden del comando:
        |
        | primero debe crearse el consultor y posteriormente
        | debe procesarse su capacitación.
        |
        */
        SafCapacitacionImportacion::query()->create([
            'id_instructor' =>
                11001,

            'programa_curso_id' =>
                9001,

            'codigo_evento' =>
                'EVT-11001',

            'curso_nombre' =>
                'Capacitación de prueba del comando',

            'fecha_inicio' =>
                '2026-07-28',

            'fecha_fin' =>
                '2026-07-28',

            'estado_curso_nombre' =>
                'Finalizado',

            'no_horas_real' =>
                8,

            'modalidad' =>
                'Virtual',

            'tipo_evento_nombre' =>
                'Capacitación',

            'cliente' =>
                'Cliente de prueba',

            'encuesta_id' =>
                null,

            'encuesta_nombre' =>
                null,

            'promedio_encuesta' =>
                null,

            'fecha_evaluacion' =>
                null,

            'estado' =>
                SafCapacitacionImportacion::ESTADO_PENDIENTE,

            'intentos' =>
                0,

            'fecha_recepcion' =>
                now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3. Ejecutar comando
        |--------------------------------------------------------------------------
        */
        $this->artisan(
            'saf:procesar-importaciones',
            [
                '--limite-instructores' => 100,
                '--limite-capacitaciones' => 100,
            ]
        )
            ->expectsOutput(
                'Iniciando procesamiento de importaciones SAF.'
            )
            ->expectsOutput(
                'Procesando instructores SAF...'
            )
            ->expectsOutput(
                'Instructores procesados: 1'
            )
            ->expectsOutput(
                'Procesando capacitaciones SAF...'
            )
            ->expectsOutput(
                'Capacitaciones procesadas: 1'
            )
            ->assertSuccessful();

        /*
        |--------------------------------------------------------------------------
        | 4. Verificar staging del instructor
        |--------------------------------------------------------------------------
        */
        $this->assertDatabaseHas(
            'tbl_saf_instructor_importacion',
            [
                'id_instructor' =>
                    11001,

                'estado' =>
                    SafInstructorImportacion::ESTADO_PROCESADO,

                'resultado_procesamiento' =>
                    'CREADO',

                'intentos' =>
                    1,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 5. Verificar staging de capacitación
        |--------------------------------------------------------------------------
        */
        $this->assertDatabaseHas(
            'tbl_saf_capacitacion_importacion',
            [
                'id_instructor' =>
                    11001,

                'codigo_evento' =>
                    'EVT-11001',

                'estado' =>
                    SafCapacitacionImportacion::ESTADO_PROCESADO,

                'resultado_procesamiento' =>
                    'CREADO',

                'intentos' =>
                    1,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 6. Verificar creación del consultor
        |--------------------------------------------------------------------------
        */
        $this->assertDatabaseHas(
            'tbl_consultor',
            [
                'id_instructor' =>
                    11001,

                'id_entidad' =>
                    1,

                'nombres' =>
                    'Instructor',

                'apellidos' =>
                    'Prueba comando',

                /*
                 * El documento SAF ya no se almacena
                 * directamente en tbl_consultor.
                 */
                'numero_identificacion' =>
                    null,

                'activo' =>
                    true,

                'vigente' =>
                    true,

                'origen_registro' =>
                    'SAF',
            ]
        );

        $consultor = DB::table(
            'tbl_consultor'
        )
            ->where(
                'id_instructor',
                11001
            )
            ->first();

        $this->assertNotNull(
            $consultor
        );

        /*
        |--------------------------------------------------------------------------
        | 7. Verificar documento
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
                    '01111111-1',

                'activo' =>
                    true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 8. Verificar correo SAF
        |--------------------------------------------------------------------------
        */
        $this->assertDatabaseHas(
            'tbl_consultor_email',
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'email' =>
                    'instructor.11001@example.com',

                'principal' =>
                    true,

                'activo' =>
                    true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 9. Verificar capacitación funcional
        |--------------------------------------------------------------------------
        */
        $this->assertDatabaseHas(
            'tbl_consultor_capacitacion_fepade',
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'programa_curso_id' =>
                    9001,

                'codigo_evento' =>
                    'EVT-11001',

                'curso_nombre' =>
                    'Capacitación de prueba del comando',

                'estado_curso_nombre' =>
                    'Finalizado',

                'no_horas_real' =>
                    8,

                'modalidad' =>
                    'Virtual',

                'tipo_evento_nombre' =>
                    'Capacitación',

                'cliente' =>
                    'Cliente de prueba',

                'fuente' =>
                    'SAF',

                'activo' =>
                    true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 10. Verificar relación de IDs locales
        |--------------------------------------------------------------------------
        */
        $registroInstructor =
            SafInstructorImportacion::query()
                ->where(
                    'id_instructor',
                    11001
                )
                ->first();

        $this->assertNotNull(
            $registroInstructor
        );

        $this->assertSame(
            (int) $consultor->id_consultor,
            (int) $registroInstructor
                ->id_registro_local
        );

        $capacitacion =
            DB::table(
                'tbl_consultor_capacitacion_fepade'
            )
                ->where(
                    'id_consultor',
                    $consultor->id_consultor
                )
                ->where(
                    'codigo_evento',
                    'EVT-11001'
                )
                ->first();

        $this->assertNotNull(
            $capacitacion
        );

        $registroCapacitacion =
            SafCapacitacionImportacion::query()
                ->where(
                    'id_instructor',
                    11001
                )
                ->where(
                    'codigo_evento',
                    'EVT-11001'
                )
                ->first();

        $this->assertNotNull(
            $registroCapacitacion
        );

        $this->assertSame(
            (int) $capacitacion
                ->id_capacitacion_fepade,

            (int) $registroCapacitacion
                ->id_registro_local
        );
    }

    public function test_it_rejects_an_invalid_instructor_limit(): void
    {
        $this->artisan(
            'saf:procesar-importaciones',
            [
                '--limite-instructores' => 0,
            ]
        )
            ->expectsOutput(
                'La opción --limite-instructores debe ser un número entero mayor que cero.'
            )
            ->assertFailed();
    }

    public function test_it_finishes_successfully_without_pending_records(): void
    {
        $this->artisan(
            'saf:procesar-importaciones'
        )
            ->expectsOutput(
                'Iniciando procesamiento de importaciones SAF.'
            )
            ->expectsOutput(
                'Instructores pendientes a procesar: 0'
            )
            ->expectsOutput(
                'Capacitaciones pendientes a procesar: 0'
            )
            ->assertSuccessful();

        $this->assertDatabaseCount(
            'tbl_sincronizacion_saf',
            1
        );
    }
}