<?php

namespace Tests\Feature\Console;

use App\Modules\Fac\Models\SafCapacitacionImportacion;
use App\Modules\Fac\Models\SafInstructorImportacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        ]);
    }

    public function test_it_processes_instructors_before_training_records(): void
    {
        SafInstructorImportacion::query()->create([
            'id_instructor' => 11001,
            'id_entidad' => 1,
            'nombres' => 'Instructor',
            'apellidos' => 'Prueba comando',
            'dui' => '00000002-2',
            'activo' => true,
            'estado' => SafInstructorImportacion::ESTADO_PENDIENTE,
            'intentos' => 0,
            'fecha_recepcion' => now(),
        ]);

        SafCapacitacionImportacion::query()->create([
            'id_instructor' => 11001,
            'codigo_evento_externo' => 'EVT-11001',
            'nombre' => 'Capacitación del comando',
            'fecha_inicio' => '2026-07-28',
            'fecha_fin' => '2026-07-28',
            'horas' => 8,
            'activo' => true,
            'estado' => SafCapacitacionImportacion::ESTADO_PENDIENTE,
            'intentos' => 0,
            'fecha_recepcion' => now(),
        ]);

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

        $this->assertDatabaseHas(
            'tbl_saf_instructor_importacion',
            [
                'id_instructor' => 11001,
                'estado' => SafInstructorImportacion::ESTADO_PROCESADO,
                'intentos' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'tbl_saf_capacitacion_importacion',
            [
                'id_instructor' => 11001,
                'codigo_evento_externo' => 'EVT-11001',
                'estado' => SafCapacitacionImportacion::ESTADO_PROCESADO,
                'intentos' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'tbl_consultor',
            [
                'id_instructor' => 11001,
                'id_entidad' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'tbl_consultor_capacitacion_fepade',
            [
                'codigo_evento_externo' => 'EVT-11001',
                'nombre_evento' => 'Capacitación del comando',
                'horas' => 8,
                'fuente' => 'SAF',
            ]
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
