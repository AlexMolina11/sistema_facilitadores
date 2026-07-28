<?php

namespace Tests\Unit\Modules\Fac\Services\Saf;

use App\Modules\Fac\Data\Saf\InstructorSafData;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Models\SincronizacionSafError;
use App\Modules\Fac\Services\Saf\SafAuditService;
use App\Modules\Fac\Services\Saf\SafInstructorSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SafInstructorSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    private SafAuditService $auditoria;

    private SafInstructorSyncService $service;

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

        $this->service = app(
            SafInstructorSyncService::class
        );
    }

    public function test_it_creates_a_consultant_from_saf(): void
    {
        $sincronizacion = $this->crearSincronizacion(
            1
        );

        $instructor = $this->crearInstructor();

        $resultado = $this->service->sincronizar(
            $instructor,
            $sincronizacion
        );

        $this->assertSame(
            SafInstructorSyncService::RESULTADO_CREADO,
            $resultado['resultado']
        );

        $this->assertInstanceOf(
            Consultor::class,
            $resultado['consultor']
        );

        $this->assertDatabaseHas(
            'tbl_consultor',
            [
                'id_instructor' => 1001,
                'id_entidad' => 1,
                'nombres' => 'Carlos',
                'apellidos' => 'Ramírez',
                'numero_identificacion' => '01234567-8',
                'tipo_identificacion' => 'DUI',
                'origen_registro' => Consultor::ORIGEN_SAF,
                'activo' => true,
                'vigente' => true,
            ]
        );

        $sincronizacion->refresh();

        $this->assertSame(
            1,
            $sincronizacion->consultores_creados
        );

        $this->assertSame(
            1,
            $sincronizacion->total_registros_procesados
        );

        $this->assertSame(
            1,
            $sincronizacion->total_registros_exitosos
        );
    }

    public function test_it_updates_an_existing_consultant(): void
    {
        $sincronizacion = $this->crearSincronizacion(
            1
        );

        $instructorInicial = $this->crearInstructor();

        $this->service->sincronizar(
            $instructorInicial,
            $sincronizacion
        );

        $instructorActualizado = new InstructorSafData(
            idInstructor: 1001,
            idEntidad: 1,
            nombres: 'Carlos Antonio',
            apellidos: 'Ramírez',
            dui: '01234567-8',
            activo: true
        );

        $resultado = $this->service->sincronizar(
            $instructorActualizado,
            $sincronizacion
        );

        $this->assertSame(
            SafInstructorSyncService::RESULTADO_ACTUALIZADO,
            $resultado['resultado']
        );

        $this->assertDatabaseHas(
            'tbl_consultor',
            [
                'id_instructor' => 1001,
                'nombres' => 'Carlos Antonio',
                'apellidos' => 'Ramírez',
            ]
        );

        $this->assertDatabaseCount(
            'tbl_consultor',
            1
        );

        $sincronizacion->refresh();

        $this->assertSame(
            1,
            $sincronizacion->consultores_creados
        );

        $this->assertSame(
            1,
            $sincronizacion->consultores_actualizados
        );
    }

    public function test_it_detects_when_there_are_no_changes(): void
    {
        $sincronizacion = $this->crearSincronizacion(
            2
        );

        $instructor = $this->crearInstructor();

        $this->service->sincronizar(
            $instructor,
            $sincronizacion
        );

        $resultado = $this->service->sincronizar(
            $instructor,
            $sincronizacion
        );

        $this->assertSame(
            SafInstructorSyncService::RESULTADO_SIN_CAMBIOS,
            $resultado['resultado']
        );

        $this->assertDatabaseCount(
            'tbl_consultor',
            1
        );

        $sincronizacion->refresh();

        $this->assertSame(
            1,
            $sincronizacion->consultores_creados
        );

        $this->assertSame(
            1,
            $sincronizacion->consultores_sin_cambios
        );

        $this->assertSame(
            2,
            $sincronizacion->total_registros_procesados
        );
    }

    public function test_it_rejects_an_instructor_from_another_entity(): void
    {
        $sincronizacion = $this->crearSincronizacion(
            1
        );

        $instructor = new InstructorSafData(
            idInstructor: 2001,
            idEntidad: 99,
            nombres: 'Instructor',
            apellidos: 'Otra Entidad',
            dui: null,
            activo: true
        );

        $resultado = $this->service->sincronizar(
            $instructor,
            $sincronizacion
        );

        $this->assertSame(
            SafInstructorSyncService::RESULTADO_OMITIDO,
            $resultado['resultado']
        );

        $this->assertNull(
            $resultado['consultor']
        );

        $this->assertDatabaseMissing(
            'tbl_consultor',
            [
                'id_instructor' => 2001,
            ]
        );

        $this->assertDatabaseHas(
            'tbl_sincronizacion_saf_error',
            [
                'tipo_registro' =>
                    SincronizacionSafError::TIPO_REGISTRO_CONSULTOR,

                'tipo_operacion' =>
                    SincronizacionSafError::OPERACION_VALIDAR,

                'codigo_error' =>
                    'ENTIDAD_NO_PERMITIDA',
            ]
        );

        $sincronizacion->refresh();

        $this->assertSame(
            1,
            $sincronizacion->consultores_con_error
        );

        $this->assertSame(
            1,
            $sincronizacion->total_registros_con_error
        );
    }

    public function test_it_marks_an_inactive_instructor(): void
    {
        $sincronizacion = $this->crearSincronizacion(
            1
        );

        $instructor = new InstructorSafData(
            idInstructor: 3001,
            idEntidad: 1,
            nombres: 'Instructor',
            apellidos: 'Inactivo',
            dui: null,
            activo: false
        );

        $resultado = $this->service->sincronizar(
            $instructor,
            $sincronizacion
        );

        $this->assertSame(
            SafInstructorSyncService::RESULTADO_CREADO,
            $resultado['resultado']
        );

        $this->assertDatabaseHas(
            'tbl_consultor',
            [
                'id_instructor' => 3001,
                'activo' => false,
                'vigente' => false,
            ]
        );
    }

    private function crearSincronizacion(
        int $total
    ): SincronizacionSaf {
        $sincronizacion = $this->auditoria->iniciar(
            SincronizacionSaf::TIPO_MANUAL
        );

        return $this->auditoria->establecerTotal(
            $sincronizacion,
            $total
        );
    }

    private function crearInstructor(): InstructorSafData
    {
        return new InstructorSafData(
            idInstructor: 1001,
            idEntidad: 1,
            nombres: 'Carlos',
            apellidos: 'Ramírez',
            dui: '01234567-8',
            activo: true
        );
    }
}