<?php

namespace Tests\Unit\Modules\Fac\Services\Saf;

use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\SafCapacitacionImportacion;
use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Services\Saf\SafAuditService;
use App\Modules\Fac\Services\Saf\SafCapacitacionImportacionProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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
            SafCapacitacionImportacionProcessor::class
        );
    }

    public function test_it_processes_a_pending_training(): void
    {
        $consultor = $this->crearConsultorSaf(
            9001
        );

        $registro =
            SafCapacitacionImportacion::query()
                ->create([
                    'id_instructor' =>
                        9001,

                    'programa_curso_id' =>
                        7001,

                    'codigo_evento' =>
                        'EVT-9001',

                    'curso_nombre' =>
                        'Liderazgo efectivo',

                    'fecha_inicio' =>
                        '2026-07-01',

                    'fecha_fin' =>
                        '2026-07-02',

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

        $ejecucion = $this->crearEjecucion(
            1
        );

        $resumen =
            $this->processor->procesar(
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

        $this->assertDatabaseHas(
            'tbl_consultor_capacitacion_fepade',
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'programa_curso_id' =>
                    7001,

                'codigo_evento' =>
                    'EVT-9001',

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
                    'SAF',

                'activo' =>
                    true,
            ]
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
                    'EVT-9001'
                )
                ->first();

        $this->assertNotNull(
            $capacitacion
        );

        $this->assertSame(
            (int) $capacitacion
                ->id_capacitacion_fepade,

            (int) $registro
                ->id_registro_local
        );
    }

    public function test_it_marks_error_when_instructor_does_not_exist(): void
    {
        $registro =
            SafCapacitacionImportacion::query()
                ->create([
                    'id_instructor' =>
                        9999,

                    'programa_curso_id' =>
                        7999,

                    'codigo_evento' =>
                        'EVT-SIN-CONSULTOR',

                    'curso_nombre' =>
                        'Capacitación sin consultor',

                    'fecha_inicio' =>
                        null,

                    'fecha_fin' =>
                        null,

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

        $ejecucion = $this->crearEjecucion(
            1
        );

        $resumen =
            $this->processor->procesar(
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
            SafCapacitacionImportacion::ESTADO_ERROR,
            $registro->estado
        );

        $this->assertSame(
            'ERROR',
            $registro->resultado_procesamiento
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
            'tbl_consultor_capacitacion_fepade',
            [
                'codigo_evento' =>
                    'EVT-SIN-CONSULTOR',
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

    public function test_it_respects_the_limit(): void
    {
        foreach (range(1, 3) as $numero) {
            $idInstructor =
                9100 + $numero;

            $this->crearConsultorSaf(
                $idInstructor
            );

            SafCapacitacionImportacion::query()
                ->create([
                    'id_instructor' =>
                        $idInstructor,

                    'programa_curso_id' =>
                        8000 + $numero,

                    'codigo_evento' =>
                        'EVT-' . $idInstructor,

                    'curso_nombre' =>
                        'Capacitación ' . $numero,

                    'fecha_inicio' =>
                        null,

                    'fecha_fin' =>
                        null,

                    'estado_curso_nombre' =>
                        'Finalizado',

                    'no_horas_real' =>
                        8,

                    'modalidad' =>
                        'Virtual',

                    'tipo_evento_nombre' =>
                        'Capacitación',

                    'cliente' =>
                        'Cliente ' . $numero,

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
        }

        $ejecucion = $this->crearEjecucion(
            2
        );

        $resumen =
            $this->processor->procesar(
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

        $this->assertSame(
            2,
            DB::table(
                'tbl_consultor_capacitacion_fepade'
            )->count()
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