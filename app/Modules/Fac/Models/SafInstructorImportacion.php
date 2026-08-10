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

    protected $table = 'tbl_saf_instructor_importacion';

    protected $primaryKey = 'id_importacion';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id_instructor',
        'id_entidad',
        'nombres',
        'apellidos',
        'dui',
        'activo',

        'estado',
        'resultado_procesamiento',
        'intentos',
        'mensaje_error',
        'fecha_recepcion',
        'fecha_procesamiento',
        'id_sincronizacion',
        'id_registro_local',
    ];

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
            'id_registro_local' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where(
            'estado',
            self::ESTADO_PENDIENTE
        );
    }

    public function scopeDeSincronizacion(
        Builder $query,
        int $idSincronizacion
    ): Builder {
        return $query->where(
            'id_sincronizacion',
            $idSincronizacion
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