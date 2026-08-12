<?php

namespace App\Modules\Fac\Data\Saf;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

final readonly class InstructorSafData
{
    /**
     * Tipos de identificación utilizados por SAF.
     */
    public const TIPO_NIT = 2;
    public const TIPO_PASAPORTE = 4;
    public const TIPO_LICENCIA_CONDUCIR = 5;
    public const TIPO_DUI = 7;

    public function __construct(
        public int $idInstructor,
        public int $idEntidad,
        public string $nombres,
        public string $apellidos,
        public ?int $tipoIdentificacion = null,
        public ?string $numeroIdentificacion = null,
        public ?string $correoSaf = null,
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

        if (
            $this->tipoIdentificacion !== null
            && ! in_array(
                $this->tipoIdentificacion,
                self::tiposIdentificacionPermitidos(),
                true
            )
        ) {
            throw new InvalidArgumentException(
                'El tipo de identificación proporcionado por SAF no es válido.'
            );
        }
    }

    /**
     * Construye el DTO a partir de los datos recibidos desde SAF.
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

                'tipo_identificacion' => [
                    'nullable',
                    'integer',
                    'in:2,4,5,7',
                ],

                'numero_identificacion' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'correo_saf' => [
                    'nullable',
                    'email',
                    'max:50',
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

                'id_instructor.min' =>
                    'El id_instructor debe ser mayor que cero.',

                'id_entidad.required' =>
                    'SAF no proporcionó el id_entidad.',

                'id_entidad.integer' =>
                    'El id_entidad debe ser numérico.',

                'id_entidad.min' =>
                    'El id_entidad debe ser mayor que cero.',

                'nombres.required' =>
                    'SAF no proporcionó los nombres del instructor.',

                'nombres.string' =>
                    'Los nombres del instructor deben ser texto.',

                'nombres.max' =>
                    'Los nombres del instructor no pueden superar los 150 caracteres.',

                'apellidos.required' =>
                    'SAF no proporcionó los apellidos del instructor.',

                'apellidos.string' =>
                    'Los apellidos del instructor deben ser texto.',

                'apellidos.max' =>
                    'Los apellidos del instructor no pueden superar los 150 caracteres.',

                'tipo_identificacion.integer' =>
                    'El tipo de identificación proporcionado por SAF debe ser numérico.',

                'tipo_identificacion.in' =>
                    'El tipo de identificación proporcionado por SAF no es reconocido. Los valores permitidos son 2, 4, 5 y 7.',

                'numero_identificacion.string' =>
                    'El número de identificación proporcionado por SAF debe ser texto.',

                'numero_identificacion.max' =>
                    'El número de identificación proporcionado por SAF no puede superar los 50 caracteres.',

                'correo_saf.email' =>
                    'El correo proporcionado por SAF no tiene un formato válido.',

                'correo_saf.max' =>
                    'El correo proporcionado por SAF no puede superar los 50 caracteres.',

                'activo.boolean' =>
                    'El estado activo proporcionado por SAF no es válido.',
            ]
        )->validate();

        return new self(
            idInstructor: (int) $normalized['id_instructor'],

            idEntidad: (int) $normalized['id_entidad'],

            nombres: $normalized['nombres'],

            apellidos: $normalized['apellidos'],

            tipoIdentificacion:
                $normalized['tipo_identificacion'] !== null
                    ? (int) $normalized['tipo_identificacion']
                    : null,

            numeroIdentificacion:
                $normalized['numero_identificacion'],

            correoSaf:
                $normalized['correo_saf'],

            activo:
                $normalized['activo'],
        );
    }

    /**
     * Convierte el DTO a la nomenclatura de la tabla staging SAF.
     */
    public function toArray(): array
    {
        return [
            'id_instructor' => $this->idInstructor,
            'id_entidad' => $this->idEntidad,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'tipo_identificacion' => $this->tipoIdentificacion,
            'numero_identificacion' => $this->numeroIdentificacion,
            'correo_saf' => $this->correoSaf,
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
     * Devuelve el nombre completo normalizado.
     */
    public function nombreCompleto(): string
    {
        return trim(
            $this->nombres . ' ' . $this->apellidos
        );
    }

    /**
     * Identificador externo utilizado por la sincronización.
     */
    public function externalId(): string
    {
        return (string) $this->idInstructor;
    }

    /**
     * Indica si SAF proporcionó información de documento.
     */
    public function tieneDocumento(): bool
    {
        return $this->tipoIdentificacion !== null
            && filled($this->numeroIdentificacion);
    }

    /**
     * Indica si SAF proporcionó correo electrónico.
     */
    public function tieneCorreoSaf(): bool
    {
        return filled($this->correoSaf);
    }

    /**
     * Determina si el instructor pertenece a la entidad
     * configurada para la integración con SAF.
     */
    public function perteneceAEntidadConfigurada(): bool
    {
        $idEntidadConfigurada = config(
            'saf.entity_id'
        );

        if (
            $idEntidadConfigurada === null
            || $idEntidadConfigurada === ''
        ) {
            return true;
        }

        return $this->idEntidad ===
            (int) $idEntidadConfigurada;
    }

    /**
     * Genera una representación estable de los datos
     * recibidos desde SAF.
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
     * Tipos de identificación que SAF puede proporcionar.
     */
    public static function tiposIdentificacionPermitidos(): array
    {
        return [
            self::TIPO_NIT,
            self::TIPO_PASAPORTE,
            self::TIPO_LICENCIA_CONDUCIR,
            self::TIPO_DUI,
        ];
    }

    /**
     * Normaliza las llaves y valores recibidos desde SAF.
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

            'id_entidad' =>
                self::normalizeInteger(
                    Arr::get(
                        $data,
                        'id_entidad'
                    )
                ),

            'nombres' =>
                self::normalizeText(
                    Arr::get(
                        $data,
                        'nombres'
                    )
                ),

            'apellidos' =>
                self::normalizeText(
                    Arr::get(
                        $data,
                        'apellidos'
                    )
                ),

            'tipo_identificacion' =>
                self::normalizeInteger(
                    Arr::get(
                        $data,
                        'tipo_identificacion'
                    )
                ),

            'numero_identificacion' =>
                self::normalizeNullableText(
                    Arr::get(
                        $data,
                        'numero_identificacion'
                    )
                ),

            'correo_saf' =>
                self::normalizeNullableText(
                    Arr::get(
                        $data,
                        'correo_saf'
                    )
                ),

            'activo' =>
                self::normalizeBoolean(
                    Arr::get(
                        $data,
                        'activo'
                    )
                ),
        ];
    }

    /**
     * Normaliza identificadores numéricos recibidos
     * como enteros o cadenas numéricas.
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
     * Normaliza booleanos provenientes de MySQL/SAF.
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
                strtolower(
                    trim($value)
                )
            ) {
                'true',
                'si',
                'sí',
                'yes',
                'activo' => true,

                'false',
                'no',
                'inactivo' => false,

                default => $value,
            };
        }

        return $value;
    }
}