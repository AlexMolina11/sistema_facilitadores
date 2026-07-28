<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SafCapacitacionImportacion extends Model
{
    public const ESTADO_PENDIENTE = 'PENDIENTE';

    public const ESTADO_EN_PROCESO = 'EN_PROCESO';

    public const ESTADO_PROCESADO = 'PROCESADO';

    public const ESTADO_ERROR = 'ERROR';

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'tbl_saf_capacitacion_importacion';

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
     * @var array<int, string>
     */
    protected $fillable = [
        'id_instructor',
        'codigo_evento_externo',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'horas',
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
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'horas' => 'integer',
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
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    /**
     * Indica si el registro está en proceso.
     */
    public function estaEnProceso(): bool
    {
        return $this->estado === self::ESTADO_EN_PROCESO;
    }

    /**
     * Indica si el registro fue procesado.
     */
    public function fueProcesado(): bool
    {
        return $this->estado === self::ESTADO_PROCESADO;
    }

    /**
     * Indica si el registro terminó con error.
     */
    public function tieneError(): bool
    {
        return $this->estado === self::ESTADO_ERROR;
    }
}
