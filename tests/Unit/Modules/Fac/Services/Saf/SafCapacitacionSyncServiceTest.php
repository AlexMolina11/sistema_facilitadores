<?php

namespace Tests\Unit\Modules\Fac\Services\Saf;

use App\Modules\Fac\Data\Saf\CapacitacionSafData;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\ConsultorCapacitacionFepade;
use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Services\Saf\SafAuditService;
use App\Modules\Fac\Services\Saf\SafCapacitacionSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SafCapacitacionSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    private SafCapacitacionSyncService $service;

    private SincronizacionSaf $sincronizacion;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'saf.hash.algorithm' => 'sha256',
        ]);

        $auditoria = app(
            SafAuditService::class
        );

        $this->service =
            new SafCapacitacionSyncService(
                $auditoria
            );

        $this->sincronizacion =
            $auditoria->iniciar(
                tipoEjecucion:
                    SincronizacionSaf::TIPO_MANUAL,

                resumenInicial: [
                    'origen' => 'TEST',
                ]
            );
    }

    public function test_it_creates_a_training_record(): void
    {
        $consultor =
            $this->crearConsultorSaf(
                5001
            );

        $capacitacion =
            $this->makeCapacitacion(
                5001
            );

        $resultado =
            $this->service->sincronizar(
                $capacitacion,
                $this->sincronizacion
            );

        $this->assertSame(
            SafCapacitacionSyncService::RESULTADO_CREADO,
            $resultado['resultado']
        );

        $this->assertNotNull(
            $resultado['capacitacion']
        );

        $this->assertDatabaseHas(
            'tbl_consultor_capacitacion_fepade',
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'programa_curso_id' =>
                    7001,

                'codigo_evento' =>
                    'EVT-5001',

                'curso_nombre' =>
                    'Liderazgo efectivo',

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
                    ConsultorCapacitacionFepade::FUENTE_SAF,

                /*
                 * Una capacitación SAF nueva nace activa.
                 */
                'activo' =>
                    1,
            ]
        );
    }

    public function test_it_updates_a_training_record(): void
    {
        $consultor =
            $this->crearConsultorSaf(
                5002
            );

        $inicial =
            $this->makeCapacitacion(
                5002
            );

        $primerResultado =
            $this->service->sincronizar(
                $inicial,
                $this->sincronizacion
            );

        $this->assertSame(
            SafCapacitacionSyncService::RESULTADO_CREADO,
            $primerResultado['resultado']
        );

        $actualizada =
            new CapacitacionSafData(
                idInstructor: 5002,
                programaCursoId: 7001,
                codigoEvento: 'EVT-5002',
                cursoNombre:
                    'Liderazgo efectivo actualizado',
                fechaInicio: null,
                fechaFin: null,
                estadoCursoNombre:
                    'Finalizado',
                noHorasReal: 12,
                modalidad:
                    'Presencial',
                tipoEventoNombre:
                    'Taller',
                cliente:
                    'Cliente actualizado',
                encuestaId:
                    9001,
                encuestaNombre:
                    'Encuesta de satisfacción',
                promedioEncuesta:
                    '4.75',
                fechaEvaluacion:
                    null
            );

        $resultado =
            $this->service->sincronizar(
                $actualizada,
                $this->sincronizacion
            );

        $this->assertSame(
            SafCapacitacionSyncService::RESULTADO_ACTUALIZADO,
            $resultado['resultado']
        );

        $this->assertDatabaseHas(
            'tbl_consultor_capacitacion_fepade',
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'codigo_evento' =>
                    'EVT-5002',

                'curso_nombre' =>
                    'Liderazgo efectivo actualizado',

                'no_horas_real' =>
                    12,

                'modalidad' =>
                    'Presencial',

                'tipo_evento_nombre' =>
                    'Taller',

                'cliente' =>
                    'Cliente actualizado',

                'encuesta_id' =>
                    9001,

                'encuesta_nombre' =>
                    'Encuesta de satisfacción',

                'promedio_encuesta' =>
                    4.75,
            ]
        );
    }

    public function test_it_detects_no_changes(): void
    {
        $this->crearConsultorSaf(
            5003
        );

        $capacitacion =
            $this->makeCapacitacion(
                5003
            );

        $primero =
            $this->service->sincronizar(
                $capacitacion,
                $this->sincronizacion
            );

        $segundo =
            $this->service->sincronizar(
                $capacitacion,
                $this->sincronizacion
            );

        $this->assertSame(
            SafCapacitacionSyncService::RESULTADO_CREADO,
            $primero['resultado']
        );

        $this->assertSame(
            SafCapacitacionSyncService::RESULTADO_SIN_CAMBIOS,
            $segundo['resultado']
        );

        $this->assertSame(
            1,
            DB::table(
                'tbl_consultor_capacitacion_fepade'
            )
                ->where(
                    'codigo_evento',
                    'EVT-5003'
                )
                ->count()
        );
    }

    public function test_it_updates_survey_data_received_later(): void
    {
        $consultor =
            $this->crearConsultorSaf(
                5004
            );

        $inicial =
            $this->makeCapacitacion(
                5004
            );

        $this->service->sincronizar(
            $inicial,
            $this->sincronizacion
        );

        /*
         * La capacitación se recibe inicialmente
         * sin encuesta.
         */
        $this->assertDatabaseHas(
            'tbl_consultor_capacitacion_fepade',
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'codigo_evento' =>
                    'EVT-5004',

                'encuesta_id' =>
                    null,

                'encuesta_nombre' =>
                    null,

                'promedio_encuesta' =>
                    null,

                'fecha_evaluacion' =>
                    null,
            ]
        );

        $conEncuesta =
            CapacitacionSafData::fromArray([
                'id_instructor' =>
                    5004,

                'programa_curso_id' =>
                    7001,

                'codigo_evento' =>
                    'EVT-5004',

                'curso_nombre' =>
                    'Liderazgo efectivo',

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
                    9100,

                'encuesta_nombre' =>
                    'Evaluación del evento',

                'promedio_encuesta' =>
                    4.80,

                'fecha_evaluacion' =>
                    '2026-08-10 14:30:00',
            ]);

        $resultado =
            $this->service->sincronizar(
                $conEncuesta,
                $this->sincronizacion
            );

        $this->assertSame(
            SafCapacitacionSyncService::RESULTADO_ACTUALIZADO,
            $resultado['resultado']
        );

        $this->assertDatabaseHas(
            'tbl_consultor_capacitacion_fepade',
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'codigo_evento' =>
                    'EVT-5004',

                'encuesta_id' =>
                    9100,

                'encuesta_nombre' =>
                    'Evaluación del evento',

                'promedio_encuesta' =>
                    4.80,

                'fecha_evaluacion' =>
                    '2026-08-10 14:30:00',
            ]
        );
    }

    public function test_it_preserves_internal_active_state_when_saf_updates_training(): void
    {
        $consultor =
            $this->crearConsultorSaf(
                5005
            );

        $inicial =
            $this->makeCapacitacion(
                5005
            );

        $resultadoInicial =
            $this->service->sincronizar(
                $inicial,
                $this->sincronizacion
            );

        $idCapacitacion =
            $resultadoInicial[
                'capacitacion'
            ]->id_capacitacion_fepade;

        /*
         * Facilitadores desactiva internamente
         * la capacitación.
         */
        DB::table(
            'tbl_consultor_capacitacion_fepade'
        )
            ->where(
                'id_capacitacion_fepade',
                $idCapacitacion
            )
            ->update([
                'activo' => 0,
            ]);

        /*
         * SAF envía una modificación de negocio.
         */
        $actualizada =
            new CapacitacionSafData(
                idInstructor: 5005,
                programaCursoId: 7001,
                codigoEvento: 'EVT-5005',
                cursoNombre:
                    'Liderazgo efectivo actualizado',
                fechaInicio: null,
                fechaFin: null,
                estadoCursoNombre:
                    'Finalizado',
                noHorasReal: 10,
                modalidad:
                    'Virtual',
                tipoEventoNombre:
                    'Capacitación',
                cliente:
                    'Cliente actualizado',
                encuestaId: null,
                encuestaNombre: null,
                promedioEncuesta: null,
                fechaEvaluacion: null
            );

        $resultado =
            $this->service->sincronizar(
                $actualizada,
                $this->sincronizacion
            );

        $this->assertSame(
            SafCapacitacionSyncService::RESULTADO_ACTUALIZADO,
            $resultado['resultado']
        );

        /*
         * Datos SAF sí cambian.
         */
        $this->assertDatabaseHas(
            'tbl_consultor_capacitacion_fepade',
            [
                'id_capacitacion_fepade' =>
                    $idCapacitacion,

                'curso_nombre' =>
                    'Liderazgo efectivo actualizado',

                'no_horas_real' =>
                    10,

                'cliente' =>
                    'Cliente actualizado',
            ]
        );

        /*
         * Pero SAF NO reactiva la capacitación.
         */
        $this->assertDatabaseHas(
            'tbl_consultor_capacitacion_fepade',
            [
                'id_capacitacion_fepade' =>
                    $idCapacitacion,

                'id_consultor' =>
                    $consultor->id_consultor,

                'activo' =>
                    0,
            ]
        );
    }

    public function test_it_returns_error_when_consultant_does_not_exist(): void
    {
        $capacitacion =
            $this->makeCapacitacion(
                9999
            );

        $resultado =
            $this->service->sincronizar(
                $capacitacion,
                $this->sincronizacion
            );

        $this->assertSame(
            SafCapacitacionSyncService::RESULTADO_ERROR,
            $resultado['resultado']
        );

        $this->assertNull(
            $resultado['capacitacion']
        );

        $this->assertDatabaseMissing(
            'tbl_consultor_capacitacion_fepade',
            [
                'codigo_evento' =>
                    'EVT-9999',
            ]
        );

        $this->assertDatabaseHas(
            'tbl_sincronizacion_saf_error',
            [
                'codigo_error' =>
                    'CONSULTOR_NO_ENCONTRADO',
            ]
        );
    }

    private function crearConsultorSaf(
        int $idInstructor
    ): Consultor {
        $consultor =
            new Consultor();

        $consultor->forceFill([
            'id_instructor' =>
                $idInstructor,

            'id_entidad' =>
                1,

            'nombres' =>
                'Instructor',

            'apellidos' =>
                'Prueba ' . $idInstructor,

            'origen_registro' =>
                Consultor::ORIGEN_SAF,

            'activo' =>
                true,

            'vigente' =>
                true,

            'fecha_ultima_sincronizacion_saf' =>
                now(),

            'hash_datos_saf' =>
                hash(
                    'sha256',
                    'consultor-' . $idInstructor
                ),
        ]);

        $consultor->save();

        return $consultor;
    }

    private function makeCapacitacion(
        int $idInstructor
    ): CapacitacionSafData {
        return new CapacitacionSafData(
            idInstructor:
                $idInstructor,

            programaCursoId:
                7001,

            codigoEvento:
                'EVT-' . $idInstructor,

            cursoNombre:
                'Liderazgo efectivo',

            fechaInicio:
                null,

            fechaFin:
                null,

            estadoCursoNombre:
                'Finalizado',

            noHorasReal:
                8,

            modalidad:
                'Virtual',

            tipoEventoNombre:
                'Capacitación',

            cliente:
                'Cliente de prueba',

            encuestaId:
                null,

            encuestaNombre:
                null,

            promedioEncuesta:
                null,

            fechaEvaluacion:
                null
        );
    }
}