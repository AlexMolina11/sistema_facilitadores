<?php

namespace App\Modules\Fac\Data\Saf;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

final readonly class InstructorSafData
{
    public function __construct(
        public int $idInstructor,
        public int $idEntidad,
        public string $nombres,
        public string $apellidos,
        public ?string $dui = null,
        public ?string $correo = null,
        public ?string $telefono = null,
        public ?bool $activo = null,
    ) {
        if ($this->idInstructor <= 0) {
            throw new InvalidArgumentException(
                'El id_instructor debe ser mayor que cero.'
            );
        }

        if ($this->idEntidad <= 0) {
            throw new InvalidArgumentException(
                'El id_entidad debe ser mayor que cero.'
            );
        }

        if ($this->nombres === '') {
            throw new InvalidArgumentException(
                'Los nombres del instructor son obligatorios.'
            );
        }

        if ($this->apellidos === '') {
            throw new InvalidArgumentException(
                'Los apellidos del instructor son obligatorios.'
            );
        }
    }

    /**
     * Construye el objeto a partir de los datos recibidos desde SAF.
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
                'id_entidad' => [
                    'required',
                    'integer',
                    'min:1',
                ],
                'nombres' => [
                    'required',
                    'string',
                    'max:150',
                ],
                'apellidos' => [
                    'required',
                    'string',
                    'max:150',
                ],
                'dui' => [
                    'nullable',
                    'string',
                    'max:25',
                ],
                'correo' => [
                    'nullable',
                    'email',
                    'max:255',
                ],
                'telefono' => [
                    'nullable',
                    'string',
                    'max:40',
                ],
                'activo' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [
                'id_instructor.required' =>
                    'SAF no proporcionó el id_instructor.',

                'id_instructor.integer' =>
                    'El id_instructor debe ser numérico.',

                'id_entidad.required' =>
                    'SAF no proporcionó el id_entidad.',

                'id_entidad.integer' =>
                    'El id_entidad debe ser numérico.',

                'nombres.required' =>
                    'SAF no proporcionó los nombres del instructor.',

                'apellidos.required' =>
                    'SAF no proporcionó los apellidos del instructor.',

                'correo.email' =>
                    'El correo proporcionado por SAF no es válido.',
            ]
        )->validate();

        return new self(
            idInstructor: (int) $normalized['id_instructor'],
            idEntidad: (int) $normalized['id_entidad'],
            nombres: $normalized['nombres'],
            apellidos: $normalized['apellidos'],
            dui: $normalized['dui'],
            correo: $normalized['correo'],
            telefono: $normalized['telefono'],
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
            'id_entidad' => $this->idEntidad,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'dui' => $this->dui,
            'correo' => $this->correo,
            'telefono' => $this->telefono,
            'activo' => $this->activo,
        ];
    }

    /**
     * Devuelve únicamente los campos con valor.
     */
    public function toFilteredArray(): array
    {
        return array_filter(
            $this->toArray(),
            static fn (mixed $value): bool => $value !== null
        );
    }

    /**
     * Devuelve el nombre completo normalizado.
     */
    public function nombreCompleto(): string
    {
        return trim($this->nombres . ' ' . $this->apellidos);
    }

    /**
     * Identificador externo utilizado por la sincronización.
     */
    public function externalId(): string
    {
        return (string) $this->idInstructor;
    }

    /**
     * Determina si pertenece a la entidad configurada para FEPADE.
     */
    public function perteneceAEntidadConfigurada(): bool
    {
        $idEntidadConfigurada = config('saf.entity_id');

        if (
            $idEntidadConfigurada === null
            || $idEntidadConfigurada === ''
        ) {
            return true;
        }

        return $this->idEntidad === (int) $idEntidadConfigurada;
    }

    /**
     * Genera una representación estable para comparar datos.
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
     * Normaliza las llaves y valores recibidos.
     */
    private static function normalizeInput(array $data): array
    {
        return [
            'id_instructor' => self::normalizeInteger(
                Arr::get($data, 'id_instructor')
            ),

            'id_entidad' => self::normalizeInteger(
                Arr::get($data, 'id_entidad')
            ),

            'nombres' => self::normalizeText(
                Arr::get($data, 'nombres')
            ),

            'apellidos' => self::normalizeText(
                Arr::get($data, 'apellidos')
            ),

            'dui' => self::normalizeDui(
                Arr::get($data, 'dui')
                    ?? Arr::get($data, 'numero_identificacion')
            ),

            'correo' => self::normalizeEmail(
                Arr::get($data, 'correo')
                    ?? Arr::get($data, 'email')
            ),

            'telefono' => self::normalizeNullableText(
                Arr::get($data, 'telefono')
            ),

            'activo' => self::normalizeBoolean(
                Arr::get($data, 'activo')
            ),
        ];
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

    private static function normalizeEmail(mixed $value): ?string
    {
        $normalized = self::normalizeNullableText($value);

        return $normalized !== null
            ? Str::lower($normalized)
            : null;
    }

    private static function normalizeDui(mixed $value): ?string
    {
        $normalized = self::normalizeNullableText($value);

        if ($normalized === null) {
            return null;
        }

        return Str::upper(
            preg_replace('/\s+/', '', $normalized) ?? $normalized
        );
    }

    private static function normalizeBoolean(
        mixed $value
    ): ?bool {
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