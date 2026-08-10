<?php

namespace App\Modules\Fac\Data\Saf;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

final readonly class CapacitacionSafData
{
    public function __construct(
        public int $idInstructor,
        public int $programaCursoId,
        public string $codigoEvento,
        public string $cursoNombre,
        public ?CarbonImmutable $fechaInicio = null,
        public ?CarbonImmutable $fechaFin = null,
        public ?string $estadoCursoNombre = null,
        public ?int $noHorasReal = null,
        public ?string $modalidad = null,
        public ?string $tipoEventoNombre = null,
        public ?string $cliente = null,
        public ?int $encuestaId = null,
        public ?string $encuestaNombre = null,
        public ?string $promedioEncuesta = null,
        public ?CarbonImmutable $fechaEvaluacion = null,
    ) {
        if ($this->idInstructor <= 0) {
            throw new InvalidArgumentException(
                'El id_instructor debe ser mayor que cero.'
            );
        }

        if ($this->programaCursoId <= 0) {
            throw new InvalidArgumentException(
                'El programa_curso_id debe ser mayor que cero.'
            );
        }

        if ($this->codigoEvento === '') {
            throw new InvalidArgumentException(
                'El codigo_evento es obligatorio.'
            );
        }

        if ($this->cursoNombre === '') {
            throw new InvalidArgumentException(
                'El curso_nombre es obligatorio.'
            );
        }

        if (
            $this->fechaInicio !== null
            && $this->fechaFin !== null
            && $this->fechaFin->isBefore(
                $this->fechaInicio
            )
        ) {
            throw new InvalidArgumentException(
                'La fecha de finalización no puede ser anterior a la fecha de inicio.'
            );
        }

        if (
            $this->noHorasReal !== null
            && $this->noHorasReal < 0
        ) {
            throw new InvalidArgumentException(
                'La cantidad de horas reales no puede ser negativa.'
            );
        }
    }

    /**
     * Construye el DTO a partir de datos provenientes de SAF.
     *
     * @throws ValidationException
     */
    public static function fromArray(
        array $data
    ): self {
        $normalized = self::normalizeInput(
            $data
        );

        validator(
            $normalized,
            [
                'id_instructor' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'programa_curso_id' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'codigo_evento' => [
                    'required',
                    'string',
                    'max:50',
                ],

                'curso_nombre' => [
                    'required',
                    'string',
                    'max:250',
                ],

                'fecha_inicio' => [
                    'nullable',
                    'date_format:Y-m-d',
                ],

                'fecha_fin' => [
                    'nullable',
                    'date_format:Y-m-d',
                    'after_or_equal:fecha_inicio',
                ],

                'estado_curso_nombre' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'no_horas_real' => [
                    'nullable',
                    'integer',
                    'min:0',
                ],

                'modalidad' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'tipo_evento_nombre' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'cliente' => [
                    'nullable',
                    'string',
                    'max:300',
                ],

                'encuesta_id' => [
                    'nullable',
                    'integer',
                    'min:1',
                ],

                'encuesta_nombre' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'promedio_encuesta' => [
                    'nullable',
                    'numeric',
                ],

                'fecha_evaluacion' => [
                    'nullable',
                    'date_format:Y-m-d H:i:s',
                ],
            ],
            [
                'id_instructor.required' =>
                    'La capacitación no contiene id_instructor.',

                'id_instructor.integer' =>
                    'El id_instructor debe ser un número entero.',

                'id_instructor.min' =>
                    'El id_instructor debe ser mayor que cero.',

                'programa_curso_id.required' =>
                    'La capacitación no contiene programa_curso_id.',

                'programa_curso_id.integer' =>
                    'El programa_curso_id debe ser un número entero.',

                'programa_curso_id.min' =>
                    'El programa_curso_id debe ser mayor que cero.',

                'codigo_evento.required' =>
                    'La capacitación no contiene codigo_evento.',

                'codigo_evento.string' =>
                    'El codigo_evento debe ser texto.',

                'codigo_evento.max' =>
                    'El codigo_evento no puede exceder los 50 caracteres.',

                'curso_nombre.required' =>
                    'La capacitación no contiene curso_nombre.',

                'curso_nombre.string' =>
                    'El curso_nombre debe ser texto.',

                'curso_nombre.max' =>
                    'El curso_nombre no puede exceder los 250 caracteres.',

                'fecha_inicio.date_format' =>
                    'La fecha de inicio debe tener el formato Y-m-d.',

                'fecha_fin.date_format' =>
                    'La fecha de finalización debe tener el formato Y-m-d.',

                'fecha_fin.after_or_equal' =>
                    'La fecha de finalización no puede ser anterior a la fecha de inicio.',

                'estado_curso_nombre.string' =>
                    'El estado del curso debe ser texto.',

                'estado_curso_nombre.max' =>
                    'El estado del curso no puede exceder los 30 caracteres.',

                'no_horas_real.integer' =>
                    'La cantidad de horas reales debe ser un número entero.',

                'no_horas_real.min' =>
                    'La cantidad de horas reales no puede ser negativa.',

                'modalidad.string' =>
                    'La modalidad debe ser texto.',

                'modalidad.max' =>
                    'La modalidad no puede exceder los 50 caracteres.',

                'tipo_evento_nombre.string' =>
                    'El tipo de evento debe ser texto.',

                'tipo_evento_nombre.max' =>
                    'El tipo de evento no puede exceder los 30 caracteres.',

                'cliente.string' =>
                    'El cliente debe ser texto.',

                'cliente.max' =>
                    'El cliente no puede exceder los 300 caracteres.',

                'encuesta_id.integer' =>
                    'El encuesta_id debe ser un número entero.',

                'encuesta_id.min' =>
                    'El encuesta_id debe ser mayor que cero.',

                'encuesta_nombre.string' =>
                    'El nombre de la encuesta debe ser texto.',

                'encuesta_nombre.max' =>
                    'El nombre de la encuesta no puede exceder los 100 caracteres.',

                'promedio_encuesta.numeric' =>
                    'El promedio de la encuesta debe ser un valor numérico.',

                'fecha_evaluacion.date_format' =>
                    'La fecha de evaluación debe tener el formato Y-m-d H:i:s.',
            ]
        )->validate();

        return new self(
            idInstructor:
                (int) $normalized['id_instructor'],

            programaCursoId:
                (int) $normalized['programa_curso_id'],

            codigoEvento:
                $normalized['codigo_evento'],

            cursoNombre:
                $normalized['curso_nombre'],

            fechaInicio:
                self::toDate(
                    $normalized['fecha_inicio']
                ),

            fechaFin:
                self::toDate(
                    $normalized['fecha_fin']
                ),

            estadoCursoNombre:
                $normalized['estado_curso_nombre'],

            noHorasReal:
                $normalized['no_horas_real'] !== null
                    ? (int) $normalized['no_horas_real']
                    : null,

            modalidad:
                $normalized['modalidad'],

            tipoEventoNombre:
                $normalized['tipo_evento_nombre'],

            cliente:
                $normalized['cliente'],

            encuestaId:
                $normalized['encuesta_id'] !== null
                    ? (int) $normalized['encuesta_id']
                    : null,

            encuestaNombre:
                $normalized['encuesta_nombre'],

            promedioEncuesta:
                $normalized['promedio_encuesta'],

            fechaEvaluacion:
                self::toDateTime(
                    $normalized['fecha_evaluacion']
                ),
        );
    }

    /**
     * Convierte el DTO a la nomenclatura utilizada
     * en la tabla staging SAF.
     */
    public function toArray(): array
    {
        return [
            'id_instructor' =>
                $this->idInstructor,

            'programa_curso_id' =>
                $this->programaCursoId,

            'codigo_evento' =>
                $this->codigoEvento,

            'curso_nombre' =>
                $this->cursoNombre,

            'fecha_inicio' =>
                $this->fechaInicio?->format(
                    'Y-m-d'
                ),

            'fecha_fin' =>
                $this->fechaFin?->format(
                    'Y-m-d'
                ),

            'estado_curso_nombre' =>
                $this->estadoCursoNombre,

            'no_horas_real' =>
                $this->noHorasReal,

            'modalidad' =>
                $this->modalidad,

            'tipo_evento_nombre' =>
                $this->tipoEventoNombre,

            'cliente' =>
                $this->cliente,

            'encuesta_id' =>
                $this->encuestaId,

            'encuesta_nombre' =>
                $this->encuestaNombre,

            'promedio_encuesta' =>
                $this->promedioEncuesta,

            'fecha_evaluacion' =>
                $this->fechaEvaluacion?->format(
                    'Y-m-d H:i:s'
                ),
        ];
    }

    /**
     * Devuelve únicamente los campos que tienen valor.
     *
     * Mantiene 0 como un valor válido.
     */
    public function toFilteredArray(): array
    {
        return array_filter(
            $this->toArray(),
            static fn (mixed $value): bool =>
                $value !== null
        );
    }

    /**
     * Identificador compuesto de una capacitación SAF.
     */
    public function externalId(): string
    {
        return $this->idInstructor
            . ':'
            . $this->codigoEvento;
    }

    /**
     * Determina si la capacitación contiene
     * información de encuesta.
     */
    public function tieneEncuesta(): bool
    {
        return $this->encuestaId !== null
            || filled($this->encuestaNombre)
            || $this->promedioEncuesta !== null
            || $this->fechaEvaluacion !== null;
    }

    /**
     * Genera una representación estable para comparación.
     */
    public function hash(): string
    {
        $algorithm = config(
            'saf.hash.algorithm',
            'sha256'
        );

        if (
            ! is_string($algorithm)
            || ! in_array(
                $algorithm,
                hash_algos(),
                true
            )
        ) {
            $algorithm = 'sha256';
        }

        $data = $this->toArray();

        ksort($data);

        return hash(
            $algorithm,
            json_encode(
                $data,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
            )
        );
    }

    /**
     * Normaliza los datos recibidos desde SAF.
     */
    private static function normalizeInput(
        array $data
    ): array {
        return [
            'id_instructor' =>
                self::normalizeInteger(
                    Arr::get(
                        $data,
                        'id_instructor'
                    )
                ),

            'programa_curso_id' =>
                self::normalizeInteger(
                    Arr::get(
                        $data,
                        'programa_curso_id'
                    )
                ),

            'codigo_evento' =>
                self::normalizeText(
                    Arr::get(
                        $data,
                        'codigo_evento'
                    )
                ),

            'curso_nombre' =>
                self::normalizeText(
                    Arr::get(
                        $data,
                        'curso_nombre'
                    )
                ),

            'fecha_inicio' =>
                self::normalizeDate(
                    Arr::get(
                        $data,
                        'fecha_inicio'
                    )
                ),

            'fecha_fin' =>
                self::normalizeDate(
                    Arr::get(
                        $data,
                        'fecha_fin'
                    )
                ),

            'estado_curso_nombre' =>
                self::normalizeNullableText(
                    Arr::get(
                        $data,
                        'estado_curso_nombre'
                    )
                ),

            'no_horas_real' =>
                self::normalizeInteger(
                    Arr::get(
                        $data,
                        'no_horas_real'
                    )
                ),

            'modalidad' =>
                self::normalizeNullableText(
                    Arr::get(
                        $data,
                        'modalidad'
                    )
                ),

            'tipo_evento_nombre' =>
                self::normalizeNullableText(
                    Arr::get(
                        $data,
                        'tipo_evento_nombre'
                    )
                ),

            'cliente' =>
                self::normalizeNullableText(
                    Arr::get(
                        $data,
                        'cliente'
                    )
                ),

            'encuesta_id' =>
                self::normalizeInteger(
                    Arr::get(
                        $data,
                        'encuesta_id'
                    )
                ),

            'encuesta_nombre' =>
                self::normalizeNullableText(
                    Arr::get(
                        $data,
                        'encuesta_nombre'
                    )
                ),

            'promedio_encuesta' =>
                self::normalizeDecimal(
                    Arr::get(
                        $data,
                        'promedio_encuesta'
                    )
                ),

            'fecha_evaluacion' =>
                self::normalizeDateTime(
                    Arr::get(
                        $data,
                        'fecha_evaluacion'
                    )
                ),
        ];
    }

    /**
     * Normaliza textos obligatorios.
     */
    private static function normalizeText(
        mixed $value
    ): string {
        return Str::of((string) $value)
            ->squish()
            ->toString();
    }

    /**
     * Normaliza textos opcionales.
     */
    private static function normalizeNullableText(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $normalized = Str::of((string) $value)
            ->squish()
            ->toString();

        return $normalized !== ''
            ? $normalized
            : null;
    }

    /**
     * Normaliza valores enteros recibidos como texto.
     */
    private static function normalizeInteger(
        mixed $value
    ): mixed {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (
            is_string($value)
            && preg_match(
                '/^\d+$/',
                trim($value)
            ) === 1
        ) {
            return (int) trim($value);
        }

        return $value;
    }

    /**
     * Normaliza valores decimales sin introducir
     * errores de precisión binaria.
     */
    private static function normalizeDecimal(
        mixed $value
    ): mixed {
        if ($value === null || $value === '') {
            return null;
        }

        if (
            is_int($value)
            || is_float($value)
            || is_numeric($value)
        ) {
            return number_format(
                (float) $value,
                2,
                '.',
                ''
            );
        }

        return $value;
    }

    /**
     * Normaliza fechas provenientes de SAF.
     */
    private static function normalizeDate(
        mixed $value
    ): mixed {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return CarbonImmutable::instance(
                $value
            )->format('Y-m-d');
        }

        $value = trim(
            (string) $value
        );

        $formats = [
            'Y-m-d',
            'd/m/Y',
            'd-m-Y',
            'Y-m-d H:i:s',
        ];

        foreach ($formats as $format) {
            try {
                $date = CarbonImmutable::createFromFormat(
                    $format,
                    $value
                );

                if ($date !== false) {
                    return $date->format(
                        'Y-m-d'
                    );
                }
            } catch (\Throwable) {
                // Se intenta el siguiente formato.
            }
        }

        return $value;
    }

    /**
     * Normaliza fecha y hora de evaluación.
     */
    private static function normalizeDateTime(
        mixed $value
    ): mixed {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return CarbonImmutable::instance(
                $value
            )->format('Y-m-d H:i:s');
        }

        $value = trim(
            (string) $value
        );

        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'd/m/Y H:i:s',
            'd/m/Y H:i',
            'Y-m-d',
        ];

        foreach ($formats as $format) {
            try {
                $date = CarbonImmutable::createFromFormat(
                    $format,
                    $value
                );

                if ($date !== false) {
                    return $date->format(
                        'Y-m-d H:i:s'
                    );
                }
            } catch (\Throwable) {
                // Se intenta el siguiente formato.
            }
        }

        return $value;
    }

    /**
     * Convierte fecha normalizada a CarbonImmutable.
     */
    private static function toDate(
        mixed $value
    ): ?CarbonImmutable {
        if ($value === null || $value === '') {
            return null;
        }

        return CarbonImmutable::createFromFormat(
            'Y-m-d',
            (string) $value
        );
    }

    /**
     * Convierte fecha y hora normalizada a CarbonImmutable.
     */
    private static function toDateTime(
        mixed $value
    ): ?CarbonImmutable {
        if ($value === null || $value === '') {
            return null;
        }

        return CarbonImmutable::createFromFormat(
            'Y-m-d H:i:s',
            (string) $value
        );
    }
}