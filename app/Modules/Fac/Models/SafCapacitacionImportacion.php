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

    protected $table = 'tbl_saf_capacitacion_importacion';

    protected $primaryKey = 'id_importacion';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id_instructor',
        'codigo_evento_externo',
        'nombre_evento',
        'tema',
        'institucion',
        'modalidad',
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

    public function scopePendientes(
        Builder $query
    ): Builder {
        return $query->where(
            'estado',
            self::ESTADO_PENDIENTE
        );
    }

    public function estaPendiente(): bool
    {
        return $this->estadoNormalizado()
            === self::ESTADO_PENDIENTE;
    }

    public function estaEnProceso(): bool
    {
        return $this->estadoNormalizado()
            === self::ESTADO_EN_PROCESO;
    }

    public function fueProcesado(): bool
    {
        return $this->estadoNormalizado()
            === self::ESTADO_PROCESADO;
    }

    public function tieneError(): bool
    {
        return $this->estadoNormalizado()
            === self::ESTADO_ERROR;
    }

    private function estadoNormalizado(): string
    {
        return strtoupper(
            trim((string) $this->estado)
        );
    }
}