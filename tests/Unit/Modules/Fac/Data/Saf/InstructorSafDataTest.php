<?php

namespace Tests\Unit\Modules\Fac\Data\Saf;

use App\Modules\Fac\Data\Saf\InstructorSafData;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Tests\TestCase;

class InstructorSafDataTest extends TestCase
{
    public function test_it_normalizes_instructor_data(): void
    {
        $data = InstructorSafData::fromArray([
            'id_instructor' => '1010',
            'id_entidad' => '1',
            'nombres' => '  Carlos   Antonio ',
            'apellidos' => ' Pérez   López ',
            'tipo_identificacion' => '7',
            'numero_identificacion' => ' 01234567-8 ',
            'correo_saf' => ' carlos@example.com ',
            'activo' => '1',
        ]);

        $this->assertSame(
            1010,
            $data->idInstructor
        );

        $this->assertSame(
            1,
            $data->idEntidad
        );

        $this->assertSame(
            'Carlos Antonio',
            $data->nombres
        );

        $this->assertSame(
            'Pérez López',
            $data->apellidos
        );

        $this->assertSame(
            InstructorSafData::TIPO_DUI,
            $data->tipoIdentificacion
        );

        $this->assertSame(
            '01234567-8',
            $data->numeroIdentificacion
        );

        $this->assertSame(
            'carlos@example.com',
            $data->correoSaf
        );

        $this->assertTrue(
            $data->activo
        );
    }

    public function test_it_converts_data_to_array(): void
    {
        $data = InstructorSafData::fromArray([
            'id_instructor' => 1010,
            'id_entidad' => 1,
            'nombres' => 'Carlos',
            'apellidos' => 'Pérez',
            'tipo_identificacion' => 7,
            'numero_identificacion' => '01234567-8',
            'correo_saf' => 'carlos@example.com',
            'activo' => false,
        ]);

        $this->assertSame(
            [
                'id_instructor' => 1010,
                'id_entidad' => 1,
                'nombres' => 'Carlos',
                'apellidos' => 'Pérez',
                'tipo_identificacion' => 7,
                'numero_identificacion' => '01234567-8',
                'correo_saf' => 'carlos@example.com',
                'activo' => false,
            ],
            $data->toArray()
        );
    }

    public function test_filtered_array_removes_null_values_but_keeps_false(): void
    {
        $data = InstructorSafData::fromArray([
            'id_instructor' => 1010,
            'id_entidad' => 1,
            'nombres' => 'Carlos',
            'apellidos' => 'Pérez',
            'activo' => false,
        ]);

        $this->assertSame(
            [
                'id_instructor' => 1010,
                'id_entidad' => 1,
                'nombres' => 'Carlos',
                'apellidos' => 'Pérez',
                'activo' => false,
            ],
            $data->toFilteredArray()
        );
    }

    public function test_it_returns_full_name(): void
    {
        $data = InstructorSafData::fromArray([
            'id_instructor' => 1010,
            'id_entidad' => 1,
            'nombres' => 'Carlos Antonio',
            'apellidos' => 'Pérez López',
        ]);

        $this->assertSame(
            'Carlos Antonio Pérez López',
            $data->nombreCompleto()
        );
    }

    public function test_it_returns_external_id(): void
    {
        $data = InstructorSafData::fromArray([
            'id_instructor' => 1010,
            'id_entidad' => 1,
            'nombres' => 'Carlos',
            'apellidos' => 'Pérez',
        ]);

        $this->assertSame(
            '1010',
            $data->externalId()
        );
    }

    public function test_it_detects_when_document_information_exists(): void
    {
        $data = InstructorSafData::fromArray([
            'id_instructor' => 1010,
            'id_entidad' => 1,
            'nombres' => 'Carlos',
            'apellidos' => 'Pérez',
            'tipo_identificacion' => 7,
            'numero_identificacion' => '01234567-8',
        ]);

        $this->assertTrue(
            $data->tieneDocumento()
        );
    }

    public function test_it_does_not_report_document_when_type_is_missing(): void
    {
        $data = InstructorSafData::fromArray([
            'id_instructor' => 1010,
            'id_entidad' => 1,
            'nombres' => 'Carlos',
            'apellidos' => 'Pérez',
            'numero_identificacion' => '01234567-8',
        ]);

        $this->assertFalse(
            $data->tieneDocumento()
        );
    }

    public function test_it_detects_saf_email(): void
    {
        $data = InstructorSafData::fromArray([
            'id_instructor' => 1010,
            'id_entidad' => 1,
            'nombres' => 'Carlos',
            'apellidos' => 'Pérez',
            'correo_saf' => 'carlos@example.com',
        ]);

        $this->assertTrue(
            $data->tieneCorreoSaf()
        );
    }

    public function test_it_accepts_all_supported_identification_types(): void
    {
        $tipos = [
            InstructorSafData::TIPO_NIT,
            InstructorSafData::TIPO_PASAPORTE,
            InstructorSafData::TIPO_LICENCIA_CONDUCIR,
            InstructorSafData::TIPO_DUI,
        ];

        foreach ($tipos as $tipo) {
            $data = InstructorSafData::fromArray([
                'id_instructor' => 1010,
                'id_entidad' => 1,
                'nombres' => 'Carlos',
                'apellidos' => 'Pérez',
                'tipo_identificacion' => $tipo,
                'numero_identificacion' => 'DOC-001',
            ]);

            $this->assertSame(
                $tipo,
                $data->tipoIdentificacion
            );
        }
    }

    public function test_it_rejects_unknown_identification_type(): void
    {
        $this->expectException(
            ValidationException::class
        );

        InstructorSafData::fromArray([
            'id_instructor' => 1010,
            'id_entidad' => 1,
            'nombres' => 'Carlos',
            'apellidos' => 'Pérez',
            'tipo_identificacion' => 99,
            'numero_identificacion' => 'DOC-001',
        ]);
    }

    public function test_it_rejects_invalid_saf_email(): void
    {
        $this->expectException(
            ValidationException::class
        );

        InstructorSafData::fromArray([
            'id_instructor' => 1010,
            'id_entidad' => 1,
            'nombres' => 'Carlos',
            'apellidos' => 'Pérez',
            'correo_saf' => 'correo-invalido',
        ]);
    }

    public function test_it_checks_configured_entity(): void
    {
        config([
            'saf.entity_id' => 1,
        ]);

        $instructorValido =
            InstructorSafData::fromArray([
                'id_instructor' => 1011,
                'id_entidad' => 1,
                'nombres' => 'Entidad',
                'apellidos' => 'Valida',
            ]);

        $instructorInvalido =
            InstructorSafData::fromArray([
                'id_instructor' => 1012,
                'id_entidad' => 2,
                'nombres' => 'Entidad',
                'apellidos' => 'Invalida',
            ]);

        $this->assertTrue(
            $instructorValido
                ->perteneceAEntidadConfigurada()
        );

        $this->assertFalse(
            $instructorInvalido
                ->perteneceAEntidadConfigurada()
        );
    }

    public function test_entity_validation_is_disabled_when_not_configured(): void
    {
        config([
            'saf.entity_id' => null,
        ]);

        $instructor = InstructorSafData::fromArray([
            'id_instructor' => 1012,
            'id_entidad' => 500,
            'nombres' => 'Entidad',
            'apellidos' => 'Libre',
        ]);

        $this->assertTrue(
            $instructor
                ->perteneceAEntidadConfigurada()
        );
    }

    public function test_hash_is_stable_for_the_same_data(): void
    {
        $primero = InstructorSafData::fromArray([
            'id_instructor' => 1013,
            'id_entidad' => 1,
            'nombres' => 'Carlos',
            'apellidos' => 'Pérez',
            'tipo_identificacion' => 7,
            'numero_identificacion' => '01234567-8',
            'correo_saf' => 'carlos@example.com',
            'activo' => true,
        ]);

        $segundo = InstructorSafData::fromArray([
            'activo' => true,
            'correo_saf' => ' carlos@example.com ',
            'numero_identificacion' => ' 01234567-8 ',
            'tipo_identificacion' => '7',
            'apellidos' => ' Pérez ',
            'nombres' => ' Carlos ',
            'id_entidad' => '1',
            'id_instructor' => '1013',
        ]);

        $this->assertSame(
            $primero->hash(),
            $segundo->hash()
        );

        $this->assertSame(
            64,
            strlen($primero->hash())
        );
    }

    public function test_hash_changes_when_data_changes(): void
    {
        $primero = InstructorSafData::fromArray([
            'id_instructor' => 1014,
            'id_entidad' => 1,
            'nombres' => 'Carlos',
            'apellidos' => 'Pérez',
            'activo' => true,
        ]);

        $segundo = InstructorSafData::fromArray([
            'id_instructor' => 1014,
            'id_entidad' => 1,
            'nombres' => 'Carlos Antonio',
            'apellidos' => 'Pérez',
            'activo' => true,
        ]);

        $this->assertNotSame(
            $primero->hash(),
            $segundo->hash()
        );
    }

    public function test_it_rejects_missing_required_fields(): void
    {
        $this->expectException(
            ValidationException::class
        );

        InstructorSafData::fromArray([
            'id_entidad' => 1,
            'nombres' => 'Carlos',
            'apellidos' => 'Pérez',
        ]);
    }

    public function test_it_rejects_an_invalid_active_value(): void
    {
        $this->expectException(
            ValidationException::class
        );

        InstructorSafData::fromArray([
            'id_instructor' => 1015,
            'id_entidad' => 1,
            'nombres' => 'Carlos',
            'apellidos' => 'Pérez',
            'activo' => 'estado-desconocido',
        ]);
    }

    public function test_constructor_rejects_an_invalid_instructor_id(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        new InstructorSafData(
            idInstructor: 0,
            idEntidad: 1,
            nombres: 'Carlos',
            apellidos: 'Pérez'
        );
    }

    public function test_constructor_rejects_an_empty_name(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        new InstructorSafData(
            idInstructor: 1016,
            idEntidad: 1,
            nombres: '',
            apellidos: 'Pérez'
        );
    }
}