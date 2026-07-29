<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SafInstructorImportacion extends Model
{
    public const ESTADO_PENDIENTE = 'PENDIENTE';
    public const ESTADO_EN_PROCESO = 'EN_PROCESO';
    public const ESTADO_PROCESADO = 'PROCESADO';
    public const ESTADO_ERROR = 'ERROR';

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'tbl_saf_instructor_importacion';

    /**
     * Llave primaria de la tabla.
     */
    protected $primaryKey = 'id_importacion';

    /**
     * Indica si la llave primaria es incremental.
     */
    public $incrementing = true;

    /**
     * Tipo de la llave primaria.
     */
    protected $keyType = 'int';

    /**
     * Campos permitidos para asignación masiva.
     *
     * Los campos de control permanecen disponibles porque son administrados
     * internamente por Laravel durante el procesamiento de importaciones.
     *
     * SAF no debe enviar ni actualizar estos campos directamente:
     *
     * - estado
     * - intentos
     * - mensaje_error
     * - fecha_recepcion
     * - fecha_procesamiento
     * - id_sincronizacion
     * - created_at
     * - updated_at
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_instructor',
        'id_entidad',
        'nombres',
        'apellidos',
        'dui',
        'activo',
        'estado',
        'intentos',
        'mensaje_error',
        'fecha_recepcion',
        'fecha_procesamiento',
        'id_sincronizacion',
    ];

    /**
     * Conversiones automáticas de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_importacion' => 'integer',
            'id_instructor' => 'integer',
            'id_entidad' => 'integer',
            'activo' => 'boolean',
            'intentos' => 'integer',
            'fecha_recepcion' => 'datetime',
            'fecha_procesamiento' => 'datetime',
            'id_sincronizacion' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Limita la consulta a registros pendientes.
     */
    public function scopePendientes(Builder $query): Builder
    {
        return $query->where(
            'estado',
            self::ESTADO_PENDIENTE
        );
    }

    /**
     * Indica si el registro está pendiente.
     */
    public function estaPendiente(): bool
    {
        return $this->estadoNormalizado()
            === self::ESTADO_PENDIENTE;
    }

    /**
     * Indica si el registro está en proceso.
     */
    public function estaEnProceso(): bool
    {
        return $this->estadoNormalizado()
            === self::ESTADO_EN_PROCESO;
    }

    /**
     * Indica si el registro fue procesado.
     */
    public function fueProcesado(): bool
    {
        return $this->estadoNormalizado()
            === self::ESTADO_PROCESADO;
    }

    /**
     * Indica si el registro terminó con error.
     */
    public function tieneError(): bool
    {
        return $this->estadoNormalizado()
            === self::ESTADO_ERROR;
    }

    /**
     * Devuelve el estado normalizado para comparaciones internas.
     */
    private function estadoNormalizado(): string
    {
        return strtoupper(
            trim((string) $this->estado)
        );
    }
}