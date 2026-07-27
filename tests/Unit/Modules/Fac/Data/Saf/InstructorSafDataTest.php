<?php

namespace Tests\Unit\Modules\Fac\Data\Saf;

use App\Modules\Fac\Data\Saf\InstructorSafData;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InstructorSafDataTest extends TestCase
{
    public function test_it_normalizes_instructor_data(): void
    {
        $data = InstructorSafData::fromArray([
            'id_instructor' => '125',
            'id_entidad' => '9',
            'nombres' => '  María    José ',
            'apellidos' => ' Pérez   López ',
            'dui' => ' 01234567-8 ',
            'correo' => ' MARIA@EJEMPLO.COM ',
            'telefono' => ' 7777-7777 ',
            'activo' => 'si',
        ]);

        $this->assertSame(125, $data->idInstructor);
        $this->assertSame(9, $data->idEntidad);
        $this->assertSame('María José', $data->nombres);
        $this->assertSame('Pérez López', $data->apellidos);
        $this->assertSame('01234567-8', $data->dui);
        $this->assertSame(
            'maria@ejemplo.com',
            $data->correo
        );
        $this->assertSame('7777-7777', $data->telefono);
        $this->assertTrue($data->activo);
    }

    public function test_it_accepts_supported_aliases(): void
    {
        $data = InstructorSafData::fromArray([
            'id_instructor' => 125,
            'id_entidad' => 9,
            'nombres' => 'María',
            'apellidos' => 'Pérez',
            'numero_identificacion' => '01234567-8',
            'email' => 'maria@ejemplo.com',
        ]);

        $this->assertSame('01234567-8', $data->dui);

        $this->assertSame(
            'maria@ejemplo.com',
            $data->correo
        );
    }

    public function test_it_returns_full_name(): void
    {
        $data = InstructorSafData::fromArray([
            'id_instructor' => 125,
            'id_entidad' => 9,
            'nombres' => 'María José',
            'apellidos' => 'Pérez López',
        ]);

        $this->assertSame(
            'María José Pérez López',
            $data->nombreCompleto()
        );
    }

    public function test_it_generates_a_stable_hash(): void
    {
        $first = InstructorSafData::fromArray([
            'id_instructor' => 125,
            'id_entidad' => 9,
            'nombres' => 'María José',
            'apellidos' => 'Pérez López',
        ]);

        $second = InstructorSafData::fromArray([
            'id_instructor' => '125',
            'id_entidad' => '9',
            'nombres' => ' María   José ',
            'apellidos' => ' Pérez  López ',
        ]);

        $this->assertSame(
            $first->hash(),
            $second->hash()
        );
    }

    public function test_it_rejects_missing_external_id(): void
    {
        $this->expectException(
            ValidationException::class
        );

        InstructorSafData::fromArray([
            'id_entidad' => 9,
            'nombres' => 'María',
            'apellidos' => 'Pérez',
        ]);
    }

    public function test_it_rejects_invalid_email(): void
    {
        $this->expectException(
            ValidationException::class
        );

        InstructorSafData::fromArray([
            'id_instructor' => 125,
            'id_entidad' => 9,
            'nombres' => 'María',
            'apellidos' => 'Pérez',
            'correo' => 'correo-no-valido',
        ]);
    }
}