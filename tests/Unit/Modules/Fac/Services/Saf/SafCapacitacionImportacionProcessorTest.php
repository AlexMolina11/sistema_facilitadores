<?php

namespace Tests\Unit\Modules\Fac\Services\Saf;

use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\SafCapacitacionImportacion;
use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Services\Saf\SafAuditService;
use App\Modules\Fac\Services\Saf\SafCapacitacionImportacionProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SafCapacitacionImportacionProcessorTest extends TestCase
{
    use RefreshDatabase;

    private SafAuditService $auditoria;

    private SafCapacitacionImportacionProcessor $processor;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'saf.audit.enabled' => true,
            'saf.audit.store_error_payload' => true,
            'saf.audit.store_exception_details' => true,
            'saf.hash.algorithm' => 'sha256',
        ]);

        $this->auditoria = app(
            SafAuditService::class
        );

        $this->processor = app(
            SafCapacitacionImportacionProcessor::class
        );
    }

    public function test_it_processes_a_pending_training(): void
    {
        $consultor = $this->crearConsultor(9001);

        $registro =
            SafCapacitacionImportacion::query()->create([
                'id_instructor' => 9001,

                'codigo_evento_externo' => 'EVT-9001',

                'nombre' => 'Servicio al cliente',

                'fecha_inicio' => '2026-07-01',

                'fecha_fin' => '2026-07-02',

                'horas' => 8,

                'activo' => true,

                'estado' => SafCapacitacionImportacion::ESTADO_PENDIENTE,

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
            SafCapacitacionImportacion::ESTADO_PROCESADO,
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

        $this->assertDatabaseHas(
            'tbl_consultor_capacitacion_fepade',
            [
                'id_consultor' => $consultor->id_consultor,

                'codigo_evento_externo' => 'EVT-9001',

                'nombre_evento' => 'Servicio al cliente',

                'horas' => 8,

                'fuente' => 'SAF',
            ]
        );
    }

    public function test_it_marks_error_when_instructor_does_not_exist(): void
    {
        $registro =
            SafCapacitacionImportacion::query()->create([
                'id_instructor' => 9999,

                'codigo_evento_externo' => 'EVT-9999',

                'nombre' => 'Capacitación sin consultor',

                'horas' => 4,

                'activo' => true,

                'estado' => SafCapacitacionImportacion::ESTADO_PENDIENTE,

                'intentos' => 0,

                'fecha_recepcion' => now(),
            ]);

        $ejecucion = $this->crearEjecucion(1);

        $resumen = $this->processor->procesar(
            $ejecucion
        );

        $this->assertSame(
            1,
            $resumen['con_error']
        );

        $registro->refresh();

        $this->assertSame(
            SafCapacitacionImportacion::ESTADO_ERROR,
            $registro->estado
        );

        $this->assertNotNull(
            $registro->mensaje_error
        );

        $this->assertNotNull(
            $registro->fecha_procesamiento
        );
    }

    public function test_it_respects_the_limit(): void
    {
        $this->crearConsultor(9101);
        $this->crearConsultor(9102);
        $this->crearConsultor(9103);

        foreach ([9101, 9102, 9103] as $idInstructor) {
            SafCapacitacionImportacion::query()->create([
                'id_instructor' => $idInstructor,

                'codigo_evento_externo' => 'EVT-'.$idInstructor,

                'nombre' => 'Capacitación '.$idInstructor,

                'horas' => 8,

                'activo' => true,

                'estado' => SafCapacitacionImportacion::ESTADO_PENDIENTE,

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
            SafCapacitacionImportacion::query()
                ->where(
                    'estado',
                    SafCapacitacionImportacion::ESTADO_PROCESADO
                )
                ->count()
        );

        $this->assertSame(
            1,
            SafCapacitacionImportacion::query()
                ->where(
                    'estado',
                    SafCapacitacionImportacion::ESTADO_PENDIENTE
                )
                ->count()
        );
    }

    private function crearConsultor(
        int $idInstructor
    ): Consultor {
        return Consultor::query()->create([
            'id_instructor' => $idInstructor,

            'id_entidad' => 1,

            'nombres' => 'Instructor',

            'apellidos' => (string) $idInstructor,

            'origen_registro' => Consultor::ORIGEN_SAF,

            'activo' => true,

            'vigente' => true,
        ]);
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
