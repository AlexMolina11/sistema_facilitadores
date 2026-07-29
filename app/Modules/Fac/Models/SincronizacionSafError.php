<?php

namespace App\Modules\Fac\Models;

use App\Modules\Seg\Models\Usuario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SincronizacionSafError extends Model
{
    /*
     |--------------------------------------------------------------------------
     | Tipos de registro
     |--------------------------------------------------------------------------
     */
    public const TIPO_REGISTRO_GENERAL = 'GENERAL';

    public const TIPO_REGISTRO_CONSULTOR = 'CONSULTOR';

    public const TIPO_REGISTRO_CAPACITACION = 'CAPACITACION';

    /*
     |--------------------------------------------------------------------------
     | Tipos de operación
     |--------------------------------------------------------------------------
     */
    public const OPERACION_CONSULTAR = 'CONSULTAR';

    public const OPERACION_CREAR = 'CREAR';

    public const OPERACION_ACTUALIZAR = 'ACTUALIZAR';

    public const OPERACION_DESACTIVAR = 'DESACTIVAR';

    public const OPERACION_VALIDAR = 'VALIDAR';

    public const OPERACION_PROCESAR = 'PROCESAR';

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'tbl_sincronizacion_saf_error';

    /**
     * Llave primaria.
     */
    protected $primaryKey = 'id_sincronizacion_saf_error';

    /**
     * Laravel administrará created_at y updated_at.
     */
    public $timestamps = true;

    /**
     * Campos disponibles para asignación masiva.
     */
    protected $fillable = [
        'id_sincronizacion_saf',
        'tipo_registro',
        'tipo_operacion',
        'id_registro_externo',
        'id_registro_local',
        'codigo_error',
        'mensaje',
        'detalle_tecnico',
        'excepcion',
        'archivo',
        'linea',
        'datos_recibidos',
        'resuelto',
        'fecha_resolucion',
        'usuario_resuelve',
        'observacion_resolucion',
    ];

    /**
     * Conversiones automáticas.
     */
    protected $casts = [
        'id_sincronizacion_saf' => 'integer',
        'id_registro_local' => 'integer',
        'linea' => 'integer',
        'datos_recibidos' => 'array',
        'resuelto' => 'boolean',
        'fecha_resolucion' => 'datetime',
        'usuario_resuelve' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Sincronización a la que pertenece el error.
     */
    public function sincronizacion(): BelongsTo
    {
        return $this->belongsTo(
            SincronizacionSaf::class,
            'id_sincronizacion_saf',
            'id_sincronizacion_saf'
        );
    }

    /**
     * Usuario que resolvió o revisó el error.
     */
    public function usuarioResolutor(): BelongsTo
    {
        return $this->belongsTo(
            Usuario::class,
            'usuario_resuelve',
            'id_usuario'
        );
    }

    /**
     * Filtra errores pendientes de resolución.
     */
    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('resuelto', false);
    }

    /**
     * Filtra errores resueltos.
     */
    public function scopeResueltos(Builder $query): Builder
    {
        return $query->where('resuelto', true);
    }

    /**
     * Filtra errores relacionados con consultores.
     */
    public function scopeDeConsultores(Builder $query): Builder
    {
        return $query->where(
            'tipo_registro',
            self::TIPO_REGISTRO_CONSULTOR
        );
    }

    /**
     * Filtra errores relacionados con capacitaciones.
     */
    public function scopeDeCapacitaciones(Builder $query): Builder
    {
        return $query->where(
            'tipo_registro',
            self::TIPO_REGISTRO_CAPACITACION
        );
    }

    /**
     * Filtra errores generales de una sincronización.
     */
    public function scopeGenerales(Builder $query): Builder
    {
        return $query->where(
            'tipo_registro',
            self::TIPO_REGISTRO_GENERAL
        );
    }

    /**
     * Determina si el error continúa pendiente.
     */
    public function estaPendiente(): bool
    {
        return $this->resuelto === false;
    }

    /**
     * Determina si el error ya fue resuelto.
     */
    public function fueResuelto(): bool
    {
        return $this->resuelto === true;
    }

    /**
     * Determina si el error corresponde a un consultor.
     */
    public function esDeConsultor(): bool
    {
        return $this->tipo_registro === self::TIPO_REGISTRO_CONSULTOR;
    }

    /**
     * Determina si el error corresponde a una capacitación.
     */
    public function esDeCapacitacion(): bool
    {
        return $this->tipo_registro === self::TIPO_REGISTRO_CAPACITACION;
    }

    /**
     * Determina si el error corresponde al proceso general.
     */
    public function esGeneral(): bool
    {
        return $this->tipo_registro === self::TIPO_REGISTRO_GENERAL;
    }

    /**
     * Marca el error como resuelto.
     */
    public function marcarComoResuelto(
        ?int $idUsuario = null,
        ?string $observacion = null
    ): bool {
        return $this->update([
            'resuelto' => true,
            'fecha_resolucion' => now(),
            'usuario_resuelve' => $idUsuario,
            'observacion_resolucion' => $observacion,
        ]);
    }

    /**
     * Reabre un error previamente resuelto.
     */
    public function reabrir(): bool
    {
        return $this->update([
            'resuelto' => false,
            'fecha_resolucion' => null,
            'usuario_resuelve' => null,
            'observacion_resolucion' => null,
        ]);
    }
}