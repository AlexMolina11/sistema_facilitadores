<?php

namespace Tests\Unit\Modules\Fac\Data\Saf;

use App\Modules\Fac\Data\Saf\CapacitacionSafData;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CapacitacionSafDataTest extends TestCase
{
    public function test_it_normalizes_training_data(): void
    {
        $data = CapacitacionSafData::fromArray([
            'id_instructor' => '125',
            'codigo_evento_externo' => ' EVT-2026-001 ',
            'nombre' => '  Liderazgo    efectivo ',
            'fecha_inicio' => '27/07/2026',
            'fecha_fin' => '28/07/2026',
            'horas' => '8,5',
            'estado' => ' Finalizado ',
            'activo' => '1',
        ]);

        $this->assertSame(125, $data->idInstructor);

        $this->assertSame(
            'EVT-2026-001',
            $data->codigoEventoExterno
        );

        $this->assertSame(
            'Liderazgo efectivo',
            $data->nombre
        );

        $this->assertSame(
            '2026-07-27',
            $data->fechaInicio?->format('Y-m-d')
        );

        $this->assertSame(
            '2026-07-28',
            $data->fechaFin?->format('Y-m-d')
        );

        $this->assertSame(8.5, $data->horas);
        $this->assertSame('Finalizado', $data->estado);
        $this->assertTrue($data->activo);
    }

    public function test_it_accepts_nombre_evento_alias(): void
    {
        $data = CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'codigo_evento_externo' => 'EVT-001',
            'nombre_evento' => 'Liderazgo',
        ]);

        $this->assertSame(
            'Liderazgo',
            $data->nombre
        );
    }

    public function test_it_generates_composite_external_id(): void
    {
        $data = CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'codigo_evento_externo' => 'EVT-001',
            'nombre' => 'Liderazgo',
        ]);

        $this->assertSame(
            '125:EVT-001',
            $data->externalId()
        );
    }

    public function test_it_rejects_end_date_before_start_date(): void
    {
        $this->expectException(
            ValidationException::class
        );

        CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'codigo_evento_externo' => 'EVT-001',
            'nombre' => 'Liderazgo',
            'fecha_inicio' => '2026-07-28',
            'fecha_fin' => '2026-07-27',
        ]);
    }

    public function test_it_rejects_negative_hours(): void
    {
        $this->expectException(
            ValidationException::class
        );

        CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'codigo_evento_externo' => 'EVT-001',
            'nombre' => 'Liderazgo',
            'horas' => -1,
        ]);
    }

    public function test_it_rejects_missing_event_code(): void
    {
        $this->expectException(
            ValidationException::class
        );

        CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'nombre' => 'Liderazgo',
        ]);
    }
}