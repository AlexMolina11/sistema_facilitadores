<?php

namespace Tests\Unit\Modules\Fac\Services\Saf;

use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Models\SincronizacionSafError;
use App\Modules\Fac\Services\Saf\SafAuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use RuntimeException;
use Tests\TestCase;

class SafAuditServiceTest extends TestCase
{
    use RefreshDatabase;

    private SafAuditService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'saf.audit.enabled' => true,
            'saf.audit.store_error_payload' => true,
            'saf.audit.store_exception_details' => true,

            'saf.sensitive_fields' => [
                'password',
                'token',
                'secret',
            ],
        ]);

        $this->service = app(SafAuditService::class);
    }

    public function test_it_starts_a_synchronization(): void
    {
        $sincronizacion = $this->service->iniciar(
            tipoEjecucion: SincronizacionSaf::TIPO_MANUAL,
            resumenInicial: [
                'origen' => 'SAF',
            ]
        );

        $this->assertNotNull(
            $sincronizacion->uuid
        );

        $this->assertSame(
            SincronizacionSaf::ESTADO_EN_PROCESO,
            $sincronizacion->estado
        );

        $this->assertSame(
            SincronizacionSaf::TIPO_MANUAL,
            $sincronizacion->tipo_ejecucion
        );

        $this->assertNotNull(
            $sincronizacion->fecha_inicio
        );

        $this->assertNull(
            $sincronizacion->fecha_fin
        );

        $this->assertSame(
            0,
            $sincronizacion->total_registros_recibidos
        );

        $this->assertSame(
            0,
            $sincronizacion->total_registros_procesados
        );

        $this->assertSame(
            0,
            $sincronizacion->total_registros_exitosos
        );

        $this->assertSame(
            0,
            $sincronizacion->total_registros_con_error
        );
    }

    public function test_it_updates_consultant_counters(): void
    {
        $sincronizacion = $this->service->iniciar(
            SincronizacionSaf::TIPO_MANUAL
        );

        $this->service->establecerTotal(
            $sincronizacion,
            4
        );

        $this->service->registrarConsultorCreado(
            $sincronizacion
        );

        $this->service->registrarConsultorActualizado(
            $sincronizacion
        );

        $this->service->registrarConsultorSinCambios(
            $sincronizacion
        );

        $sincronizacion =
            $this->service->registrarConsultorConError(
                $sincronizacion
            );

        $this->assertSame(
            4,
            $sincronizacion->total_registros_recibidos
        );

        $this->assertSame(
            4,
            $sincronizacion->total_registros_procesados
        );

        $this->assertSame(
            3,
            $sincronizacion->total_registros_exitosos
        );

        $this->assertSame(
            1,
            $sincronizacion->total_registros_con_error
        );

        $this->assertSame(
            1,
            $sincronizacion->consultores_creados
        );

        $this->assertSame(
            1,
            $sincronizacion->consultores_actualizados
        );

        $this->assertSame(
            1,
            $sincronizacion->consultores_sin_cambios
        );

        $this->assertSame(
            1,
            $sincronizacion->consultores_con_error
        );
    }

    public function test_it_updates_training_counters(): void
    {
        $sincronizacion = $this->service->iniciar(
            SincronizacionSaf::TIPO_AUTOMATICA
        );

        $this->service->establecerTotal(
            $sincronizacion,
            5
        );

        $this->service->registrarCapacitacionCreada(
            $sincronizacion
        );

        $this->service->registrarCapacitacionActualizada(
            $sincronizacion
        );

        $this->service->registrarCapacitacionSinCambios(
            $sincronizacion
        );

        $this->service->registrarCapacitacionDesactivada(
            $sincronizacion
        );

        $sincronizacion =
            $this->service->registrarCapacitacionConError(
                $sincronizacion
            );

        $this->assertSame(
            5,
            $sincronizacion->total_registros_recibidos
        );

        $this->assertSame(
            5,
            $sincronizacion->total_registros_procesados
        );

        $this->assertSame(
            4,
            $sincronizacion->total_registros_exitosos
        );

        $this->assertSame(
            1,
            $sincronizacion->total_registros_con_error
        );

        $this->assertSame(
            1,
            $sincronizacion->capacitaciones_creadas
        );

        $this->assertSame(
            1,
            $sincronizacion->capacitaciones_actualizadas
        );

        $this->assertSame(
            1,
            $sincronizacion->capacitaciones_sin_cambios
        );

        $this->assertSame(
            1,
            $sincronizacion->capacitaciones_desactivadas
        );

        $this->assertSame(
            1,
            $sincronizacion->capacitaciones_con_error
        );
    }

    public function test_it_registers_an_individual_error(): void
    {
        $sincronizacion = $this->service->iniciar(
            SincronizacionSaf::TIPO_MANUAL
        );

        $error = $this->service->registrarError(
            sincronizacion: $sincronizacion,

            tipoRegistro:
                SincronizacionSafError::TIPO_REGISTRO_CONSULTOR,

            tipoOperacion:
                SincronizacionSafError::OPERACION_VALIDAR,

            mensaje: 'El DUI no es válido.',

            opciones: [
                'id_registro_externo' => 'SAF-100',
                'codigo_error' => 'VALIDATION_ERROR',

                'datos_recibidos' => [
                    'nombres' => 'Carlos',
                    'password' => 'clave-real',
                ],
            ]
        );

        $this->assertSame(
            'El DUI no es válido.',
            $error->mensaje
        );

        $this->assertSame(
            SincronizacionSafError::TIPO_REGISTRO_CONSULTOR,
            $error->tipo_registro
        );

        $this->assertSame(
            SincronizacionSafError::OPERACION_VALIDAR,
            $error->tipo_operacion
        );

        $this->assertSame(
            '[PROTEGIDO]',
            $error->datos_recibidos['password']
        );

        $this->assertFalse(
            $error->resuelto
        );

        $this->assertDatabaseHas(
            'tbl_sincronizacion_saf_error',
            [
                'id_sincronizacion_saf' =>
                    $sincronizacion->id_sincronizacion_saf,

                'mensaje' => 'El DUI no es válido.',
                'resuelto' => false,
            ]
        );
    }

    public function test_it_registers_an_exception(): void
    {
        $sincronizacion = $this->service->iniciar(
            SincronizacionSaf::TIPO_MANUAL
        );

        $exception = new RuntimeException(
            'Error inesperado de SAF.'
        );

        $error = $this->service->registrarExcepcion(
            sincronizacion: $sincronizacion,
            exception: $exception,
            datosRecibidos: [
                'token' => 'token-real',
            ],
            idRegistroExterno: 'SAF-200'
        );

        $this->assertSame(
            'Error inesperado de SAF.',
            $error->mensaje
        );

        $this->assertSame(
            RuntimeException::class,
            $error->excepcion
        );

        $this->assertSame(
            SincronizacionSafError::TIPO_REGISTRO_GENERAL,
            $error->tipo_registro
        );

        $this->assertSame(
            SincronizacionSafError::OPERACION_PROCESAR,
            $error->tipo_operacion
        );

        $this->assertSame(
            '[PROTEGIDO]',
            $error->datos_recibidos['token']
        );
    }

    public function test_it_sanitizes_nested_sensitive_data(): void
    {
        $resultado =
            $this->service->datosParaAuditoria([
                'nombres' => 'María',
                'token' => 'abc123',

                'credenciales' => [
                    'secret' => 'secreto',
                    'usuario' => 'maria',
                ],
            ]);

        $this->assertSame(
            '[PROTEGIDO]',
            $resultado['token']
        );

        $this->assertSame(
            '[PROTEGIDO]',
            $resultado['credenciales']['secret']
        );

        $this->assertSame(
            'maria',
            $resultado['credenciales']['usuario']
        );
    }

    public function test_it_finishes_successfully_without_errors(): void
    {
        $sincronizacion = $this->service->iniciar(
            SincronizacionSaf::TIPO_MANUAL
        );

        $this->service->establecerTotal(
            $sincronizacion,
            1
        );

        $this->service->registrarConsultorCreado(
            $sincronizacion
        );

        $sincronizacion =
            $this->service->finalizar(
                $sincronizacion
            );

        $this->assertSame(
            SincronizacionSaf::ESTADO_COMPLETADA,
            $sincronizacion->estado
        );

        $this->assertNotNull(
            $sincronizacion->fecha_fin
        );

        $this->assertSame(
            'La sincronización finalizó correctamente.',
            $sincronizacion->mensaje
        );
    }

    public function test_it_finishes_with_errors(): void
    {
        $sincronizacion = $this->service->iniciar(
            SincronizacionSaf::TIPO_MANUAL
        );

        $this->service->registrarError(
            sincronizacion: $sincronizacion,

            tipoRegistro:
                SincronizacionSafError::TIPO_REGISTRO_GENERAL,

            tipoOperacion:
                SincronizacionSafError::OPERACION_PROCESAR,

            mensaje: 'Error controlado.'
        );

        $sincronizacion =
            $this->service->finalizar(
                $sincronizacion
            );

        $this->assertSame(
            SincronizacionSaf::ESTADO_COMPLETADA_CON_ERRORES,
            $sincronizacion->estado
        );

        $this->assertSame(
            'La sincronización finalizó con errores.',
            $sincronizacion->mensaje
        );
    }

    public function test_it_marks_execution_as_failed(): void
    {
        $sincronizacion = $this->service->iniciar(
            SincronizacionSaf::TIPO_MANUAL
        );

        $sincronizacion =
            $this->service->marcarComoFallida(
                $sincronizacion,
                new RuntimeException(
                    'La ejecución se interrumpió.'
                )
            );

        $this->assertSame(
            SincronizacionSaf::ESTADO_FALLIDA,
            $sincronizacion->estado
        );

        $this->assertSame(
            'La ejecución se interrumpió.',
            $sincronizacion->mensaje
        );

        $this->assertSame(
            'La ejecución se interrumpió.',
            $sincronizacion->resumen['motivo_fallo']
        );
    }

    public function test_it_does_not_modify_a_closed_execution(): void
    {
        $sincronizacion = $this->service->iniciar(
            SincronizacionSaf::TIPO_MANUAL
        );

        $sincronizacion =
            $this->service->finalizar(
                $sincronizacion
            );

        $this->expectException(
            LogicException::class
        );

        $this->service->registrarConsultorCreado(
            $sincronizacion
        );
    }

    public function test_it_does_not_store_payload_when_disabled(): void
    {
        config([
            'saf.audit.store_error_payload' => false,
        ]);

        $resultado =
            $this->service->datosParaAuditoria([
                'nombres' => 'Carlos',
                'token' => 'abc123',
            ]);

        $this->assertSame([], $resultado);
    }
}