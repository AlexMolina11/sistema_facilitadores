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
        public string $nombre,
        public ?CarbonImmutable $fechaInicio = null,
        public ?CarbonImmutable $fechaFin = null,
        public ?float $horas = null,
        public ?string $estado = null,
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

        if ($this->nombre === '') {
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
                    'max:150',
                ],
                'nombre' => [
                    'required',
                    'string',
                    'max:255',
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
                    'numeric',
                    'min:0',
                ],
                'estado' => [
                    'nullable',
                    'string',
                    'max:100',
                ],
                'activo' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [
                'id_instructor.required' =>
                    'La capacitación no contiene id_instructor.',

                'codigo_evento_externo.required' =>
                    'La capacitación no contiene codigo_evento_externo.',

                'nombre.required' =>
                    'La capacitación no contiene nombre.',

                'fecha_inicio.date_format' =>
                    'La fecha de inicio debe tener el formato Y-m-d.',

                'fecha_fin.date_format' =>
                    'La fecha de finalización debe tener el formato Y-m-d.',

                'fecha_fin.after_or_equal' =>
                    'La fecha de finalización no puede ser anterior a la fecha de inicio.',

                'horas.numeric' =>
                    'La cantidad de horas debe ser numérica.',

                'horas.min' =>
                    'La cantidad de horas no puede ser negativa.',
            ]
        )->validate();

        return new self(
            idInstructor: (int) $normalized['id_instructor'],

            codigoEventoExterno:
                $normalized['codigo_evento_externo'],

            nombre: $normalized['nombre'],

            fechaInicio: self::toDate(
                $normalized['fecha_inicio']
            ),

            fechaFin: self::toDate(
                $normalized['fecha_fin']
            ),

            horas: $normalized['horas'] !== null
                ? (float) $normalized['horas']
                : null,

            estado: $normalized['estado'],

            activo: $normalized['activo'],
        );
    }

    /**
     * Convierte el objeto a la nomenclatura SAF.
     */
    public function toArray(): array
    {
        return [
            'id_instructor' => $this->idInstructor,

            'codigo_evento_externo' =>
                $this->codigoEventoExterno,

            'nombre' => $this->nombre,

            'fecha_inicio' =>
                $this->fechaInicio?->format('Y-m-d'),

            'fecha_fin' =>
                $this->fechaFin?->format('Y-m-d'),

            'horas' => $this->horas,

            'estado' => $this->estado,

            'activo' => $this->activo,
        ];
    }

    /**
     * Devuelve únicamente campos con valor.
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
            . ':'
            . $this->codigoEventoExterno;
    }

    /**
     * Genera una representación estable para comparación.
     */
    public function hash(): string
    {
        $algorithm = config('saf.hash.algorithm', 'sha256');

        if (! in_array($algorithm, hash_algos(), true)) {
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
     * Normaliza la entrada.
     */
    private static function normalizeInput(array $data): array
    {
        return [
            'id_instructor' => self::normalizeInteger(
                Arr::get($data, 'id_instructor')
            ),

            'codigo_evento_externo' => self::normalizeText(
                Arr::get($data, 'codigo_evento_externo')
            ),

            'nombre' => self::normalizeText(
                Arr::get($data, 'nombre')
                    ?? Arr::get($data, 'nombre_evento')
            ),

            'fecha_inicio' => self::normalizeDate(
                Arr::get($data, 'fecha_inicio')
            ),

            'fecha_fin' => self::normalizeDate(
                Arr::get($data, 'fecha_fin')
            ),

            'horas' => self::normalizeFloat(
                Arr::get($data, 'horas')
            ),

            'estado' => self::normalizeNullableText(
                Arr::get($data, 'estado')
            ),

            'activo' => self::normalizeBoolean(
                Arr::get($data, 'activo')
            ),
        ];
    }

    private static function normalizeText(mixed $value): string
    {
        return Str::of((string) $value)
            ->squish()
            ->toString();
    }

    private static function normalizeNullableText(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $normalized = self::normalizeText($value);

        return $normalized !== '' ? $normalized : null;
    }

    private static function normalizeInteger(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (
            is_string($value)
            && preg_match('/^\d+$/', trim($value)) === 1
        ) {
            return (int) trim($value);
        }

        return $value;
    }

    private static function normalizeFloat(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_float($value) || is_int($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $normalized = str_replace(',', '.', trim($value));

            if (is_numeric($normalized)) {
                return (float) $normalized;
            }
        }

        return $value;
    }

    private static function normalizeDate(mixed $value): mixed
    {
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
                $date = CarbonImmutable::createFromFormat(
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

    private static function toDate(
        ?string $value
    ): ?CarbonImmutable {
        return $value !== null
            ? CarbonImmutable::createFromFormat('Y-m-d', $value)
            : null;
    }

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
            return match (Str::lower(trim($value))) {
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