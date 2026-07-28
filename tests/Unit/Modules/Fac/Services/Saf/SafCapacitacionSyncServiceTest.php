<?php

namespace Tests\Unit\Modules\Fac\Services\Saf;

use App\Modules\Fac\Data\Saf\CapacitacionSafData;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Services\Saf\SafAuditService;
use App\Modules\Fac\Services\Saf\SafCapacitacionSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SafCapacitacionSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    private SafAuditService $auditoria;

    private SafCapacitacionSyncService $service;

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

        $this->service = app(
            SafCapacitacionSyncService::class
        );
    }

    public function test_it_creates_a_training_record(): void
    {
        $consultor = $this->crearConsultor(8001);
        $ejecucion = $this->crearEjecucion(1);

        $resultado = $this->service->sincronizar(
            $this->crearCapacitacion(8001),
            $ejecucion
        );

        $this->assertSame(
            SafCapacitacionSyncService::RESULTADO_CREADO,
            $resultado['resultado']
        );

        $this->assertDatabaseHas(
            'tbl_consultor_capacitacion_fepade',
            [
                'id_consultor' => $consultor->id_consultor,

                'codigo_evento_externo' => 'EVT-8001',

                'nombre_evento' => 'Liderazgo efectivo',

                'horas' => 8,

                'fuente' => 'SAF',

                'activo' => true,
            ]
        );

        $ejecucion->refresh();

        $this->assertSame(
            1,
            $ejecucion->capacitaciones_creadas
        );
    }

    public function test_it_updates_a_training_record(): void
    {
        $this->crearConsultor(8002);
        $ejecucion = $this->crearEjecucion(2);

        $this->service->sincronizar(
            $this->crearCapacitacion(8002),
            $ejecucion
        );

        $actualizada = new CapacitacionSafData(
            idInstructor: 8002,
            codigoEventoExterno: 'EVT-8002',
            nombre: 'Liderazgo avanzado',
            fechaInicio: null,
            fechaFin: null,
            horas: 16,
            activo: true
        );

        $resultado = $this->service->sincronizar(
            $actualizada,
            $ejecucion
        );

        $this->assertSame(
            SafCapacitacionSyncService::RESULTADO_ACTUALIZADO,
            $resultado['resultado']
        );

        $this->assertDatabaseHas(
            'tbl_consultor_capacitacion_fepade',
            [
                'codigo_evento_externo' => 'EVT-8002',

                'nombre_evento' => 'Liderazgo avanzado',

                'horas' => 16,
            ]
        );
    }

    public function test_it_detects_no_changes(): void
    {
        $this->crearConsultor(8003);
        $ejecucion = $this->crearEjecucion(2);
        $capacitacion = $this->crearCapacitacion(8003);

        $this->service->sincronizar(
            $capacitacion,
            $ejecucion
        );

        $resultado = $this->service->sincronizar(
            $capacitacion,
            $ejecucion
        );

        $this->assertSame(
            SafCapacitacionSyncService::RESULTADO_SIN_CAMBIOS,
            $resultado['resultado']
        );

        $ejecucion->refresh();

        $this->assertSame(
            1,
            $ejecucion->capacitaciones_sin_cambios
        );
    }

    public function test_it_marks_training_as_inactive(): void
    {
        $this->crearConsultor(8004);
        $ejecucion = $this->crearEjecucion(2);

        $this->service->sincronizar(
            $this->crearCapacitacion(8004),
            $ejecucion
        );

        $inactiva = new CapacitacionSafData(
            idInstructor: 8004,
            codigoEventoExterno: 'EVT-8004',
            nombre: 'Liderazgo efectivo',
            fechaInicio: null,
            fechaFin: null,
            horas: 8,
            activo: false
        );

        $resultado = $this->service->sincronizar(
            $inactiva,
            $ejecucion
        );

        $this->assertSame(
            SafCapacitacionSyncService::RESULTADO_DESACTIVADO,
            $resultado['resultado']
        );

        $this->assertDatabaseHas(
            'tbl_consultor_capacitacion_fepade',
            [
                'codigo_evento_externo' => 'EVT-8004',

                'activo' => false,
            ]
        );

        $ejecucion->refresh();

        $this->assertSame(
            1,
            $ejecucion->capacitaciones_desactivadas
        );
    }

    public function test_it_returns_error_when_consultant_does_not_exist(): void
    {
        $ejecucion = $this->crearEjecucion(1);

        $resultado = $this->service->sincronizar(
            $this->crearCapacitacion(8999),
            $ejecucion
        );

        $this->assertSame(
            SafCapacitacionSyncService::RESULTADO_ERROR,
            $resultado['resultado']
        );

        $this->assertDatabaseMissing(
            'tbl_consultor_capacitacion_fepade',
            [
                'codigo_evento_externo' => 'EVT-8999',
            ]
        );

        $ejecucion->refresh();

        $this->assertSame(
            1,
            $ejecucion->capacitaciones_con_error
        );
    }

    private function crearConsultor(
        int $idInstructor
    ): Consultor {
        return Consultor::query()->create([
            'id_instructor' => $idInstructor,

            'id_entidad' => 1,

            'nombres' => 'Instructor',

            'apellidos' => 'Prueba',

            'origen_registro' => Consultor::ORIGEN_SAF,

            'activo' => true,

            'vigente' => true,
        ]);
    }

    private function crearCapacitacion(
        int $idInstructor
    ): CapacitacionSafData {
        return new CapacitacionSafData(
            idInstructor: $idInstructor,
            codigoEventoExterno: 'EVT-'.$idInstructor,
            nombre: 'Liderazgo efectivo',
            fechaInicio: null,
            fechaFin: null,
            horas: 8,
            activo: true
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
