<?php

namespace Tests\Unit\Modules\Fac\Data\Saf;

use App\Modules\Fac\Data\Saf\CapacitacionSafData;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Tests\TestCase;

class CapacitacionSafDataTest extends TestCase
{
    public function test_it_normalizes_training_data(): void
    {
        $data = CapacitacionSafData::fromArray([
            'id_instructor' => '125',
            'programa_curso_id' => '5001',
            'codigo_evento' => ' EVT-2026-001 ',
            'curso_nombre' => '  Liderazgo    efectivo ',
            'fecha_inicio' => '27/07/2026',
            'fecha_fin' => '28/07/2026',
            'estado_curso_nombre' => ' Finalizado ',
            'no_horas_real' => '8',
            'modalidad' => ' Virtual ',
            'tipo_evento_nombre' => ' Capacitación ',
            'cliente' => ' FEPADE ',
            'encuesta_id' => '7001',
            'encuesta_nombre' => ' Encuesta de satisfacción ',
            'promedio_encuesta' => '4.75',
            'fecha_evaluacion' => '29/07/2026 14:30',
        ]);

        $this->assertSame(
            125,
            $data->idInstructor
        );

        $this->assertSame(
            5001,
            $data->programaCursoId
        );

        $this->assertSame(
            'EVT-2026-001',
            $data->codigoEvento
        );

        $this->assertSame(
            'Liderazgo efectivo',
            $data->cursoNombre
        );

        $this->assertSame(
            '2026-07-27',
            $data->fechaInicio?->format('Y-m-d')
        );

        $this->assertSame(
            '2026-07-28',
            $data->fechaFin?->format('Y-m-d')
        );

        $this->assertSame(
            'Finalizado',
            $data->estadoCursoNombre
        );

        $this->assertSame(
            8,
            $data->noHorasReal
        );

        $this->assertSame(
            'Virtual',
            $data->modalidad
        );

        $this->assertSame(
            'Capacitación',
            $data->tipoEventoNombre
        );

        $this->assertSame(
            'FEPADE',
            $data->cliente
        );

        $this->assertSame(
            7001,
            $data->encuestaId
        );

        $this->assertSame(
            'Encuesta de satisfacción',
            $data->encuestaNombre
        );

        $this->assertSame(
            '4.75',
            $data->promedioEncuesta
        );

        $this->assertSame(
            '2026-07-29 14:30:00',
            $data->fechaEvaluacion?->format(
                'Y-m-d H:i:s'
            )
        );
    }

    public function test_it_generates_composite_external_id(): void
    {
        $data = CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'programa_curso_id' => 5001,
            'codigo_evento' => 'EVT-001',
            'curso_nombre' => 'Liderazgo',
        ]);

        $this->assertSame(
            '125:EVT-001',
            $data->externalId()
        );
    }

    public function test_it_converts_data_to_array(): void
    {
        $data = CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'programa_curso_id' => 5001,
            'codigo_evento' => 'EVT-001',
            'curso_nombre' => 'Liderazgo',
            'fecha_inicio' => '2026-07-27',
            'fecha_fin' => '2026-07-28',
            'estado_curso_nombre' => 'Finalizado',
            'no_horas_real' => 8,
            'modalidad' => 'Virtual',
            'tipo_evento_nombre' => 'Capacitación',
            'cliente' => 'FEPADE',
            'encuesta_id' => 7001,
            'encuesta_nombre' => 'Encuesta final',
            'promedio_encuesta' => 4.75,
            'fecha_evaluacion' => '2026-07-29 14:30:00',
        ]);

        $this->assertSame(
            [
                'id_instructor' => 125,
                'programa_curso_id' => 5001,
                'codigo_evento' => 'EVT-001',
                'curso_nombre' => 'Liderazgo',
                'fecha_inicio' => '2026-07-27',
                'fecha_fin' => '2026-07-28',
                'estado_curso_nombre' => 'Finalizado',
                'no_horas_real' => 8,
                'modalidad' => 'Virtual',
                'tipo_evento_nombre' => 'Capacitación',
                'cliente' => 'FEPADE',
                'encuesta_id' => 7001,
                'encuesta_nombre' => 'Encuesta final',
                'promedio_encuesta' => '4.75',
                'fecha_evaluacion' => '2026-07-29 14:30:00',
            ],
            $data->toArray()
        );
    }

    public function test_filtered_array_keeps_zero_hours(): void
    {
        $data = CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'programa_curso_id' => 5001,
            'codigo_evento' => 'EVT-001',
            'curso_nombre' => 'Liderazgo',
            'no_horas_real' => 0,
        ]);

        $this->assertSame(
            [
                'id_instructor' => 125,
                'programa_curso_id' => 5001,
                'codigo_evento' => 'EVT-001',
                'curso_nombre' => 'Liderazgo',
                'no_horas_real' => 0,
            ],
            $data->toFilteredArray()
        );
    }

    public function test_it_detects_training_with_survey_data(): void
    {
        $data = CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'programa_curso_id' => 5001,
            'codigo_evento' => 'EVT-001',
            'curso_nombre' => 'Liderazgo',
            'encuesta_id' => 7001,
            'encuesta_nombre' => 'Encuesta final',
            'promedio_encuesta' => 4.50,
            'fecha_evaluacion' => '2026-07-29 14:30:00',
        ]);

        $this->assertTrue(
            $data->tieneEncuesta()
        );
    }

    public function test_it_detects_training_without_survey_data(): void
    {
        $data = CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'programa_curso_id' => 5001,
            'codigo_evento' => 'EVT-001',
            'curso_nombre' => 'Liderazgo',
        ]);

        $this->assertFalse(
            $data->tieneEncuesta()
        );
    }

    public function test_hash_is_stable_for_same_data(): void
    {
        $first = CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'programa_curso_id' => 5001,
            'codigo_evento' => 'EVT-001',
            'curso_nombre' => 'Liderazgo',
            'no_horas_real' => 8,
            'promedio_encuesta' => 4.50,
        ]);

        $second = CapacitacionSafData::fromArray([
            'promedio_encuesta' => '4.50',
            'no_horas_real' => '8',
            'curso_nombre' => ' Liderazgo ',
            'codigo_evento' => ' EVT-001 ',
            'programa_curso_id' => '5001',
            'id_instructor' => '125',
        ]);

        $this->assertSame(
            $first->hash(),
            $second->hash()
        );

        $this->assertSame(
            64,
            strlen($first->hash())
        );
    }

    public function test_hash_changes_when_training_data_changes(): void
    {
        $first = CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'programa_curso_id' => 5001,
            'codigo_evento' => 'EVT-001',
            'curso_nombre' => 'Liderazgo',
            'no_horas_real' => 8,
        ]);

        $second = CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'programa_curso_id' => 5001,
            'codigo_evento' => 'EVT-001',
            'curso_nombre' => 'Liderazgo',
            'no_horas_real' => 12,
        ]);

        $this->assertNotSame(
            $first->hash(),
            $second->hash()
        );
    }

    public function test_it_rejects_end_date_before_start_date(): void
    {
        $this->expectException(
            ValidationException::class
        );

        CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'programa_curso_id' => 5001,
            'codigo_evento' => 'EVT-001',
            'curso_nombre' => 'Liderazgo',
            'fecha_inicio' => '2026-07-28',
            'fecha_fin' => '2026-07-27',
        ]);
    }

    public function test_it_rejects_negative_real_hours(): void
    {
        $this->expectException(
            ValidationException::class
        );

        CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'programa_curso_id' => 5001,
            'codigo_evento' => 'EVT-001',
            'curso_nombre' => 'Liderazgo',
            'no_horas_real' => -1,
        ]);
    }

    public function test_it_rejects_decimal_real_hours(): void
    {
        $this->expectException(
            ValidationException::class
        );

        CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'programa_curso_id' => 5001,
            'codigo_evento' => 'EVT-001',
            'curso_nombre' => 'Liderazgo',
            'no_horas_real' => '8.5',
        ]);
    }

    public function test_it_rejects_missing_program_course_id(): void
    {
        $this->expectException(
            ValidationException::class
        );

        CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'codigo_evento' => 'EVT-001',
            'curso_nombre' => 'Liderazgo',
        ]);
    }

    public function test_it_rejects_missing_event_code(): void
    {
        $this->expectException(
            ValidationException::class
        );

        CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'programa_curso_id' => 5001,
            'curso_nombre' => 'Liderazgo',
        ]);
    }

    public function test_it_rejects_event_code_longer_than_50_characters(): void
    {
        $this->expectException(
            ValidationException::class
        );

        CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'programa_curso_id' => 5001,
            'codigo_evento' => str_repeat('A', 51),
            'curso_nombre' => 'Liderazgo',
        ]);
    }

    public function test_it_rejects_course_name_longer_than_250_characters(): void
    {
        $this->expectException(
            ValidationException::class
        );

        CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'programa_curso_id' => 5001,
            'codigo_evento' => 'EVT-001',
            'curso_nombre' => str_repeat('A', 251),
        ]);
    }

    public function test_it_rejects_invalid_survey_id(): void
    {
        $this->expectException(
            ValidationException::class
        );

        CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'programa_curso_id' => 5001,
            'codigo_evento' => 'EVT-001',
            'curso_nombre' => 'Liderazgo',
            'encuesta_id' => 0,
        ]);
    }

    public function test_it_rejects_invalid_evaluation_date(): void
    {
        $this->expectException(
            ValidationException::class
        );

        CapacitacionSafData::fromArray([
            'id_instructor' => 125,
            'programa_curso_id' => 5001,
            'codigo_evento' => 'EVT-001',
            'curso_nombre' => 'Liderazgo',
            'fecha_evaluacion' => 'fecha-invalida',
        ]);
    }

    public function test_constructor_rejects_invalid_program_course_id(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        new CapacitacionSafData(
            idInstructor: 125,
            programaCursoId: 0,
            codigoEvento: 'EVT-001',
            cursoNombre: 'Liderazgo'
        );
    }
}