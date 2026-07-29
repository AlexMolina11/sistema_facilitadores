<?php

namespace App\Modules\Fac\Data\Saf;

use Carbon\CarbonImmutable;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

final readonly class CapacitacionSafData
{
    public function __construct(
        public int $idInstructor,
        public string $codigoEventoExterno,
        public string $nombreEvento,
        public ?string $tema = null,
        public ?string $institucion = null,
        public ?string $modalidad = null,
        public ?CarbonImmutable $fechaInicio = null,
        public ?CarbonImmutable $fechaFin = null,
        public ?int $horas = null,
        public ?bool $activo = null,
    ) {
        if ($this->idInstructor <= 0) {
            throw new InvalidArgumentException(
                'El id_instructor debe ser mayor que cero.'
            );
        }

        if ($this->codigoEventoExterno === '') {
            throw new InvalidArgumentException(
                'El codigo_evento_externo es obligatorio.'
            );
        }

        if ($this->nombreEvento === '') {
            throw new InvalidArgumentException(
                'El nombre de la capacitación es obligatorio.'
            );
        }

        if (
            $this->fechaInicio !== null
            && $this->fechaFin !== null
            && $this->fechaFin->isBefore($this->fechaInicio)
        ) {
            throw new InvalidArgumentException(
                'La fecha de finalización no puede ser anterior a la fecha de inicio.'
            );
        }

        if ($this->horas !== null && $this->horas < 0) {
            throw new InvalidArgumentException(
                'La cantidad de horas no puede ser negativa.'
            );
        }
    }

    /**
     * Construye el objeto a partir de datos provenientes de SAF.
     *
     * @throws ValidationException
     */
    public static function fromArray(array $data): self
    {
        $normalized = self::normalizeInput($data);

        validator(
            $normalized,
            [
                'id_instructor' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'codigo_evento_externo' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'nombre_evento' => [
                    'required',
                    'string',
                    'max:250',
                ],

                'tema' => [
                    'nullable',
                    'string',
                    'max:250',
                ],

                'institucion' => [
                    'nullable',
                    'string',
                    'max:250',
                ],

                'modalidad' => [
                    'nullable',
                    'string',
                    'max:100',
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

                'horas' => [
                    'nullable',
                    'integer',
                    'min:0',
                ],

                'activo' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [
                'id_instructor.required' => 'La capacitación no contiene id_instructor.',

                'id_instructor.integer' => 'El id_instructor debe ser un número entero.',

                'id_instructor.min' => 'El id_instructor debe ser mayor que cero.',

                'codigo_evento_externo.required' => 'La capacitación no contiene codigo_evento_externo.',

                'codigo_evento_externo.max' => 'El codigo_evento_externo no puede exceder los 100 caracteres.',

                'nombre.required' => 'La capacitación no contiene nombre.',

                'nombre.max' => 'El nombre de la capacitación no puede exceder los 250 caracteres.',

                'fecha_inicio.date_format' => 'La fecha de inicio debe tener el formato Y-m-d.',

                'fecha_fin.date_format' => 'La fecha de finalización debe tener el formato Y-m-d.',

                'fecha_fin.after_or_equal' => 'La fecha de finalización no puede ser anterior a la fecha de inicio.',

                'horas.integer' => 'La cantidad de horas debe ser un número entero.',

                'horas.min' => 'La cantidad de horas no puede ser negativa.',

                'activo.boolean' => 'El estado activo debe ser verdadero o falso.',
            ]
        )->validate();

        return new self(
            idInstructor: (int) $normalized['id_instructor'],

            codigoEventoExterno: $normalized['codigo_evento_externo'],

            nombreEvento: $normalized['nombre_evento'],

            tema: $normalized['tema'] !== ''
                ? $normalized['tema']
                : null,

            institucion: $normalized['institucion'] !== ''
                ? $normalized['institucion']
                : null,

            modalidad: $normalized['modalidad'] !== ''
                ? $normalized['modalidad']
                : null,

            fechaInicio: self::toDate($normalized['fecha_inicio']),

            fechaFin: self::toDate($normalized['fecha_fin']),

            horas: $normalized['horas'] !== null
                    ? (int) $normalized['horas']
                    : null,

            activo: $normalized['activo'],
        );
    }

    /**
     * Convierte el objeto a la nomenclatura utilizada por SAF.
     */
    public function toArray(): array
    {
        return [
            'id_instructor' => $this->idInstructor,

            'codigo_evento_externo' => $this->codigoEventoExterno,

            'nombre_evento' => $this->nombreEvento,

            'tema' => $this->tema,

            'institucion' => $this->institucion,

            'modalidad' => $this->modalidad,

            'fecha_inicio' => $this->fechaInicio?->format('Y-m-d'),

            'fecha_fin' => $this->fechaFin?->format('Y-m-d'),

            'horas' => $this->horas,

            'activo' => $this->activo,
        ];
    }

    /**
     * Devuelve únicamente los campos que tienen valor.
     *
     * Mantiene false y 0 como valores válidos.
     */
    public function toFilteredArray(): array
    {
        return array_filter(
            $this->toArray(),
            static fn (mixed $value): bool => $value !== null
        );
    }

    /**
     * Identificador compuesto de la capacitación.
     *
     * El código del evento puede repetirse para distintos instructores.
     */
    public function externalId(): string
    {
        return $this->idInstructor
            .':'
            .$this->codigoEventoExterno;
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
            'id_instructor' => self::normalizeInteger(
                Arr::get(
                    $data,
                    'id_instructor'
                )
            ),

            'codigo_evento_externo' => self::normalizeText(
                Arr::get(
                    $data,
                    'codigo_evento_externo'
                )
            ),

            'nombre_evento' => self::normalizeText(
                Arr::get(
                    $data,
                    'nombre_evento'
                ) ?? Arr::get(
                    $data,
                    'nombre'
                )
            ),

            'tema' => self::normalizeText(
                Arr::get(
                    $data,
                    'tema'
                )
            ),

            'institucion' => self::normalizeText(
                Arr::get(
                    $data,
                    'institucion'
                )
            ),

            'modalidad' => self::normalizeText(
                Arr::get(
                    $data,
                    'modalidad'
                )
            ),

            'fecha_inicio' => self::normalizeDate(
                Arr::get(
                    $data,
                    'fecha_inicio'
                )
            ),

            'fecha_fin' => self::normalizeDate(
                Arr::get(
                    $data,
                    'fecha_fin'
                )
            ),

            'horas' => self::normalizeInteger(
                Arr::get(
                    $data,
                    'horas'
                )
            ),

            'activo' => self::normalizeBoolean(
                Arr::get(
                    $data,
                    'activo'
                )
            ),
        ];
    }

    /**
     * Normaliza textos y elimina espacios repetidos.
     */
    private static function normalizeText(
        mixed $value
    ): string {
        return Str::of((string) $value)
            ->squish()
            ->toString();
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
     * Normaliza las fechas aceptadas por la integración.
     */
    private static function normalizeDate(
        mixed $value
    ): mixed {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof CarbonImmutable) {
            return $value->format('Y-m-d');
        }

        $value = trim((string) $value);

        $formats = [
            'Y-m-d',
            'd/m/Y',
            'd-m-Y',
            'Y-m-d H:i:s',
        ];

        foreach ($formats as $format) {
            try {
                $date =
                    CarbonImmutable::createFromFormat(
                        $format,
                        $value
                    );

                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            } catch (InvalidFormatException) {
                // Se intenta el siguiente formato.
            }
        }

        return $value;
    }

    /**
     * Convierte una fecha normalizada a CarbonImmutable.
     */
    private static function toDate(
        ?string $value
    ): ?CarbonImmutable {
        return $value !== null
            ? CarbonImmutable::createFromFormat(
                'Y-m-d',
                $value
            )
            : null;
    }

    /**
     * Normaliza valores booleanos recibidos desde SAF.
     */
    private static function normalizeBoolean(
        mixed $value
    ): mixed {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        if ($value === 1 || $value === '1') {
            return true;
        }

        if ($value === 0 || $value === '0') {
            return false;
        }

        if (is_string($value)) {
            return match (
                Str::lower(trim($value))
            ) {
                'true',
                'si',
                'sí',
                'activo',
                'active' => true,

                'false',
                'no',
                'inactivo',
                'inactive' => false,

                default => $value,
            };
        }

        return $value;
    }
}
