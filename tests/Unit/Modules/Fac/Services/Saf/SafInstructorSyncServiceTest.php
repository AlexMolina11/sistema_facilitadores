<?php

namespace Tests\Unit\Modules\Fac\Services\Saf;

use App\Modules\Fac\Data\Saf\InstructorSafData;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Services\Saf\SafAuditService;
use App\Modules\Fac\Services\Saf\SafInstructorSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SafInstructorSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    private SafInstructorSyncService $service;

    private SincronizacionSaf $sincronizacion;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'saf.entity_id' => 1,
            'saf.hash.algorithm' => 'sha256',
        ]);

        $auditoria = app(
            SafAuditService::class
        );

        $this->service =
            new SafInstructorSyncService(
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

    public function test_it_creates_a_consultant_from_saf(): void
    {
        $instructor =
            $this->makeInstructor();

        $resultado =
            $this->service->sincronizar(
                $instructor,
                $this->sincronizacion
            );

        $this->assertSame(
            SafInstructorSyncService::RESULTADO_CREADO,
            $resultado['resultado']
        );

        $this->assertNotNull(
            $resultado['consultor']
        );

        $this->assertDatabaseHas(
            'tbl_consultor',
            [
                'id_instructor' => 1001,
                'id_entidad' => 1,
                'nombres' => 'Carlos',
                'apellidos' => 'Ramírez',
                'activo' => 1,
                'vigente' => 1,
                'origen_registro' =>
                    Consultor::ORIGEN_SAF,
            ]
        );

        /*
         * El documento SAF NO debe guardarse
         * en tbl_consultor.
         */
        $this->assertDatabaseHas(
            'tbl_consultor',
            [
                'id_instructor' => 1001,
                'numero_identificacion' => null,
            ]
        );

        /*
         * SAF 7 = DUI
         * Facilitadores 2 = DUI
         */
        $this->assertDatabaseHas(
            'tbl_consultor_documento',
            [
                'id_consultor' =>
                    $resultado['consultor']
                        ->id_consultor,

                'id_tipo_documento' => 2,
                'numero' => '01234567-8',
                'activo' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'tbl_consultor_email',
            [
                'id_consultor' =>
                    $resultado['consultor']
                        ->id_consultor,

                'email' =>
                    'carlos.ramirez@example.com',

                'principal' => 1,
                'activo' => 1,
            ]
        );
    }

    public function test_it_updates_an_existing_consultant(): void
    {
        $inicial =
            $this->service->sincronizar(
                $this->makeInstructor(),
                $this->sincronizacion
            );

        $consultor =
            $inicial['consultor'];

        $actualizado =
            new InstructorSafData(
                idInstructor: 1001,
                idEntidad: 1,
                nombres: 'Carlos Antonio',
                apellidos: 'Ramírez López',
                tipoIdentificacion:
                    InstructorSafData::TIPO_DUI,
                numeroIdentificacion:
                    '08888888-8',
                correoSaf:
                    'nuevo.correo@example.com',
                activo: true
            );

        $resultado =
            $this->service->sincronizar(
                $actualizado,
                $this->sincronizacion
            );

        $this->assertSame(
            SafInstructorSyncService::RESULTADO_ACTUALIZADO,
            $resultado['resultado']
        );

        $this->assertDatabaseHas(
            'tbl_consultor',
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'nombres' =>
                    'Carlos Antonio',

                'apellidos' =>
                    'Ramírez López',
            ]
        );

        /*
         * Debe existir un solo DUI activo
         * con el número actualizado.
         */
        $this->assertDatabaseHas(
            'tbl_consultor_documento',
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'id_tipo_documento' => 2,
                'numero' => '08888888-8',
                'activo' => 1,
            ]
        );

        $this->assertDatabaseMissing(
            'tbl_consultor_documento',
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'id_tipo_documento' => 2,
                'numero' => '01234567-8',
                'activo' => 1,
            ]
        );

        /*
         * El nuevo correo queda como principal.
         */
        $this->assertDatabaseHas(
            'tbl_consultor_email',
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'email' =>
                    'nuevo.correo@example.com',

                'principal' => 1,
                'activo' => 1,
            ]
        );

        /*
         * El correo anterior se conserva,
         * pero deja de ser principal.
         */
        $this->assertDatabaseHas(
            'tbl_consultor_email',
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'email' =>
                    'carlos.ramirez@example.com',

                'principal' => 0,
                'activo' => 1,
            ]
        );
    }

    public function test_it_detects_when_there_are_no_changes(): void
    {
        $instructor =
            $this->makeInstructor();

        $primero =
            $this->service->sincronizar(
                $instructor,
                $this->sincronizacion
            );

        $segundo =
            $this->service->sincronizar(
                $instructor,
                $this->sincronizacion
            );

        $this->assertSame(
            SafInstructorSyncService::RESULTADO_CREADO,
            $primero['resultado']
        );

        $this->assertSame(
            SafInstructorSyncService::RESULTADO_SIN_CAMBIOS,
            $segundo['resultado']
        );

        $this->assertSame(
            1,
            DB::table(
                'tbl_consultor'
            )
                ->where(
                    'id_instructor',
                    1001
                )
                ->count()
        );

        $this->assertSame(
            1,
            DB::table(
                'tbl_consultor_documento'
            )
                ->where(
                    'id_consultor',
                    $primero['consultor']
                        ->id_consultor
                )
                ->where(
                    'id_tipo_documento',
                    2
                )
                ->count()
        );

        $this->assertSame(
            1,
            DB::table(
                'tbl_consultor_email'
            )
                ->where(
                    'id_consultor',
                    $primero['consultor']
                        ->id_consultor
                )
                ->where(
                    'email',
                    'carlos.ramirez@example.com'
                )
                ->count()
        );
    }

    public function test_it_maps_all_supported_document_types(): void
    {
        $casos = [
            [
                'id_instructor' => 1101,
                'tipo_saf' =>
                    InstructorSafData::TIPO_NIT,
                'tipo_local' => 1,
                'numero' => '0614-010101-001-1',
            ],
            [
                'id_instructor' => 1102,
                'tipo_saf' =>
                    InstructorSafData::TIPO_PASAPORTE,
                'tipo_local' => 4,
                'numero' => 'P12345678',
            ],
            [
                'id_instructor' => 1103,
                'tipo_saf' =>
                    InstructorSafData::TIPO_LICENCIA_CONDUCIR,
                'tipo_local' => 6,
                'numero' => 'LIC-1103',
            ],
            [
                'id_instructor' => 1104,
                'tipo_saf' =>
                    InstructorSafData::TIPO_DUI,
                'tipo_local' => 2,
                'numero' => '01111111-1',
            ],
        ];

        foreach ($casos as $caso) {
            $instructor =
                new InstructorSafData(
                    idInstructor:
                        $caso['id_instructor'],

                    idEntidad: 1,

                    nombres: 'Instructor',

                    apellidos:
                        'Documento',

                    tipoIdentificacion:
                        $caso['tipo_saf'],

                    numeroIdentificacion:
                        $caso['numero'],

                    correoSaf: null,

                    activo: true
                );

            $resultado =
                $this->service->sincronizar(
                    $instructor,
                    $this->sincronizacion
                );

            $this->assertSame(
                SafInstructorSyncService::RESULTADO_CREADO,
                $resultado['resultado']
            );

            $this->assertDatabaseHas(
                'tbl_consultor_documento',
                [
                    'id_consultor' =>
                        $resultado['consultor']
                            ->id_consultor,

                    'id_tipo_documento' =>
                        $caso['tipo_local'],

                    'numero' =>
                        $caso['numero'],

                    'activo' => 1,
                ]
            );
        }
    }

    public function test_it_preserves_previous_email_as_secondary(): void
    {
        $primero =
            $this->service->sincronizar(
                $this->makeInstructor(),
                $this->sincronizacion
            );

        $consultor =
            $primero['consultor'];

        $nuevo =
            new InstructorSafData(
                idInstructor: 1001,
                idEntidad: 1,
                nombres: 'Carlos',
                apellidos: 'Ramírez',
                tipoIdentificacion:
                    InstructorSafData::TIPO_DUI,
                numeroIdentificacion:
                    '01234567-8',
                correoSaf:
                    'nuevo@example.com',
                activo: true
            );

        $this->service->sincronizar(
            $nuevo,
            $this->sincronizacion
        );

        $this->assertDatabaseHas(
            'tbl_consultor_email',
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'email' =>
                    'carlos.ramirez@example.com',

                'principal' => 0,
                'activo' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'tbl_consultor_email',
            [
                'id_consultor' =>
                    $consultor->id_consultor,

                'email' =>
                    'nuevo@example.com',

                'principal' => 1,
                'activo' => 1,
            ]
        );
    }

    public function test_it_rejects_an_instructor_from_another_entity(): void
    {
        $instructor =
            new InstructorSafData(
                idInstructor: 2001,
                idEntidad: 99,
                nombres: 'Instructor',
                apellidos: 'Otra Entidad',
                tipoIdentificacion: null,
                numeroIdentificacion: null,
                correoSaf: null,
                activo: true
            );

        $resultado =
            $this->service->sincronizar(
                $instructor,
                $this->sincronizacion
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
                'codigo_error' =>
                    'ENTIDAD_NO_PERMITIDA',
            ]
        );
    }

    public function test_it_marks_an_inactive_instructor(): void
    {
        $instructor =
            new InstructorSafData(
                idInstructor: 3001,
                idEntidad: 1,
                nombres: 'Instructor',
                apellidos: 'Inactivo',
                tipoIdentificacion: null,
                numeroIdentificacion: null,
                correoSaf: null,
                activo: false
            );

        $resultado =
            $this->service->sincronizar(
                $instructor,
                $this->sincronizacion
            );

        $this->assertSame(
            SafInstructorSyncService::RESULTADO_CREADO,
            $resultado['resultado']
        );

        $this->assertDatabaseHas(
            'tbl_consultor',
            [
                'id_instructor' => 3001,
                'activo' => 0,
                'vigente' => 0,
            ]
        );
    }

    public function test_it_does_not_create_document_when_saf_does_not_send_one(): void
    {
        $instructor =
            new InstructorSafData(
                idInstructor: 4001,
                idEntidad: 1,
                nombres: 'Sin',
                apellidos: 'Documento',
                tipoIdentificacion: null,
                numeroIdentificacion: null,
                correoSaf: null,
                activo: true
            );

        $resultado =
            $this->service->sincronizar(
                $instructor,
                $this->sincronizacion
            );

        $this->assertSame(
            SafInstructorSyncService::RESULTADO_CREADO,
            $resultado['resultado']
        );

        $this->assertDatabaseMissing(
            'tbl_consultor_documento',
            [
                'id_consultor' =>
                    $resultado['consultor']
                        ->id_consultor,
            ]
        );
    }

    private function makeInstructor(): InstructorSafData
    {
        return new InstructorSafData(
            idInstructor: 1001,
            idEntidad: 1,
            nombres: 'Carlos',
            apellidos: 'Ramírez',
            tipoIdentificacion:
                InstructorSafData::TIPO_DUI,
            numeroIdentificacion:
                '01234567-8',
            correoSaf:
                'carlos.ramirez@example.com',
            activo: true
        );
    }
}