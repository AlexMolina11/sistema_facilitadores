<?php

namespace Tests\Unit\Modules\Fac\Services\Saf;

use App\Modules\Fac\Models\SafInstructorImportacion;
use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Services\Saf\SafAuditService;
use App\Modules\Fac\Services\Saf\SafInstructorImportacionProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    }

    public function test_it_processes_a_pending_instructor(): void
    {
        $registro = SafInstructorImportacion::query()->create([
            'id_instructor' => 5001,
            'id_entidad' => 1,
            'nombres' => 'Carlos',
            'apellidos' => 'Ramírez',
            'dui' => '01234567-8',
            'activo' => true,
            'estado' => SafInstructorImportacion::ESTADO_PENDIENTE,
            'intentos' => 0,
            'fecha_recepcion' => now(),
        ]);

        $ejecucion = $this->crearEjecucion(1);

        $resumen = $this->processor->procesar(
            $ejecucion
        );

        $this->assertSame(
            [
                'detectados' => 1,
                'procesados' => 1,
                'exitosos' => 1,
                'con_error' => 0,
            ],
            $resumen
        );

        $registro->refresh();

        $this->assertSame(
            SafInstructorImportacion::ESTADO_PROCESADO,
            $registro->estado
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

        $this->assertDatabaseHas(
            'tbl_consultor',
            [
                'id_instructor' => 5001,
                'id_entidad' => 1,
                'nombres' => 'Carlos',
                'apellidos' => 'Ramírez',
                'numero_identificacion' => '01234567-8',
                'activo' => true,
                'vigente' => true,
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
            'dui' => null,
            'activo' => true,
            'estado' => SafInstructorImportacion::ESTADO_PROCESADO,
            'intentos' => 1,
            'fecha_recepcion' => now(),
            'fecha_procesamiento' => now(),
        ]);

        $ejecucion = $this->crearEjecucion(0);

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
        $registro = SafInstructorImportacion::query()->create([
            'id_instructor' => 5003,
            'id_entidad' => 99,
            'nombres' => 'Instructor',
            'apellidos' => 'Entidad incorrecta',
            'dui' => null,
            'activo' => true,
            'estado' => SafInstructorImportacion::ESTADO_PENDIENTE,
            'intentos' => 0,
            'fecha_recepcion' => now(),
        ]);

        $ejecucion = $this->crearEjecucion(1);

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

        $this->assertDatabaseMissing(
            'tbl_consultor',
            [
                'id_instructor' => 5003,
            ]
        );
    }

    public function test_it_respects_the_processing_limit(): void
    {
        foreach (range(1, 3) as $numero) {
            SafInstructorImportacion::query()->create([
                'id_instructor' => 6000 + $numero,
                'id_entidad' => 1,
                'nombres' => 'Instructor',
                'apellidos' => 'Número '.$numero,
                'dui' => null,
                'activo' => true,
                'estado' => SafInstructorImportacion::ESTADO_PENDIENTE,
                'intentos' => 0,
                'fecha_recepcion' => now(),
            ]);
        }

        $ejecucion = $this->crearEjecucion(2);

        $resumen = $this->processor->procesar(
            ejecucion: $ejecucion,
            limite: 2
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
        $ejecucion = $this->auditoria->iniciar(
            SincronizacionSaf::TIPO_MANUAL
        );

        return $this->auditoria->establecerTotal(
            $ejecucion,
            $total
        );
    }
}
