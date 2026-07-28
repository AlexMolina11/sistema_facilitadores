<?php

namespace Tests\Unit\Modules\Fac\Data\Saf;

use App\Modules\Fac\Data\Saf\InstructorSafData;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Tests\TestCase;

class InstructorSafDataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'saf.entity_id' => 1,
            'saf.hash.algorithm' => 'sha256',
        ]);
    }

    public function test_it_creates_the_dto_from_valid_data(): void
    {
        $instructor = InstructorSafData::fromArray([
            'id_instructor' => '1001',
            'id_entidad' => '1',
            'nombres' => '  Carlos   Antonio ',
            'apellidos' => ' Ramírez   López ',
            'dui' => ' 01234567-8 ',
            'activo' => '1',
        ]);

        $this->assertSame(
            1001,
            $instructor->idInstructor
        );

        $this->assertSame(
            1,
            $instructor->idEntidad
        );

        $this->assertSame(
            'Carlos Antonio',
            $instructor->nombres
        );

        $this->assertSame(
            'Ramírez López',
            $instructor->apellidos
        );

        $this->assertSame(
            '01234567-8',
            $instructor->dui
        );

        $this->assertTrue(
            $instructor->activo
        );
    }

    public function test_it_accepts_numero_identificacion_as_dui_alias(): void
    {
        $instructor = InstructorSafData::fromArray([
            'id_instructor' => 1002,
            'id_entidad' => 1,
            'nombres' => 'Ana',
            'apellidos' => 'Martínez',
            'numero_identificacion' =>
                ' 01234567-8 ',
            'activo' => true,
        ]);

        $this->assertSame(
            '01234567-8',
            $instructor->dui
        );
    }

    public function test_it_normalizes_boolean_values(): void
    {
        $activo = InstructorSafData::fromArray([
            'id_instructor' => 1003,
            'id_entidad' => 1,
            'nombres' => 'Mario',
            'apellidos' => 'Activo',
            'activo' => 'sí',
        ]);

        $inactivo = InstructorSafData::fromArray([
            'id_instructor' => 1004,
            'id_entidad' => 1,
            'nombres' => 'Mario',
            'apellidos' => 'Inactivo',
            'activo' => 'inactivo',
        ]);

        $this->assertTrue(
            $activo->activo
        );

        $this->assertFalse(
            $inactivo->activo
        );
    }

    public function test_it_allows_nullable_optional_values(): void
    {
        $instructor = InstructorSafData::fromArray([
            'id_instructor' => 1005,
            'id_entidad' => 1,
            'nombres' => 'Andrea',
            'apellidos' => 'Gómez',
        ]);

        $this->assertNull(
            $instructor->dui
        );

        $this->assertNull(
            $instructor->activo
        );
    }

    public function test_to_array_returns_only_the_contract_fields(): void
    {
        $instructor = InstructorSafData::fromArray([
            'id_instructor' => 1006,
            'id_entidad' => 1,
            'nombres' => 'José',
            'apellidos' => 'Hernández',
            'dui' => '01234567-8',
            'activo' => true,

            // Estos campos deben ser ignorados.
            'correo' => 'jose@ejemplo.com',
            'telefono' => '7000-0000',
        ]);

        $this->assertSame(
            [
                'id_instructor' => 1006,
                'id_entidad' => 1,
                'nombres' => 'José',
                'apellidos' => 'Hernández',
                'dui' => '01234567-8',
                'activo' => true,
            ],
            $instructor->toArray()
        );

        $this->assertArrayNotHasKey(
            'correo',
            $instructor->toArray()
        );

        $this->assertArrayNotHasKey(
            'telefono',
            $instructor->toArray()
        );
    }

    public function test_filtered_array_removes_only_null_values(): void
    {
        $instructor = InstructorSafData::fromArray([
            'id_instructor' => 1007,
            'id_entidad' => 1,
            'nombres' => 'Luis',
            'apellidos' => 'Inactivo',
            'dui' => null,
            'activo' => false,
        ]);

        $this->assertSame(
            [
                'id_instructor' => 1007,
                'id_entidad' => 1,
                'nombres' => 'Luis',
                'apellidos' => 'Inactivo',
                'activo' => false,
            ],
            $instructor->toFilteredArray()
        );
    }

    public function test_it_returns_the_normalized_full_name(): void
    {
        $instructor = InstructorSafData::fromArray([
            'id_instructor' => 1008,
            'id_entidad' => 1,
            'nombres' => 'María Elena',
            'apellidos' => 'Pérez López',
        ]);

        $this->assertSame(
            'María Elena Pérez López',
            $instructor->nombreCompleto()
        );
    }

    public function test_external_id_returns_the_instructor_id_as_string(): void
    {
        $instructor = InstructorSafData::fromArray([
            'id_instructor' => 1009,
            'id_entidad' => 1,
            'nombres' => 'Pedro',
            'apellidos' => 'Ramírez',
        ]);

        $this->assertSame(
            '1009',
            $instructor->externalId()
        );
    }

    public function test_it_detects_the_configured_entity(): void
    {
        $instructorValido =
            InstructorSafData::fromArray([
                'id_instructor' => 1010,
                'id_entidad' => 1,
                'nombres' => 'Entidad',
                'apellidos' => 'Correcta',
            ]);

        $instructorInvalido =
            InstructorSafData::fromArray([
                'id_instructor' => 1011,
                'id_entidad' => 99,
                'nombres' => 'Entidad',
                'apellidos' => 'Incorrecta',
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
            'dui' => '01234567-8',
            'activo' => true,
        ]);

        $segundo = InstructorSafData::fromArray([
            'activo' => true,
            'dui' => '01234567-8',
            'apellidos' => 'Pérez',
            'nombres' => 'Carlos',
            'id_entidad' => 1,
            'id_instructor' => 1013,
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