<?php

namespace App\Modules\Fac\Models;

use App\Modules\Seg\Models\Usuario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SincronizacionSaf extends Model
{
    /*
     |--------------------------------------------------------------------------
     | Tipos de ejecución
     |--------------------------------------------------------------------------
     */
    public const TIPO_MANUAL = 'MANUAL';

    public const TIPO_AUTOMATICA = 'AUTOMATICA';

    /*
     |--------------------------------------------------------------------------
     | Estados de sincronización
     |--------------------------------------------------------------------------
     */
    public const ESTADO_PENDIENTE = 'PENDIENTE';

    public const ESTADO_EN_PROCESO = 'EN_PROCESO';

    public const ESTADO_COMPLETADA = 'COMPLETADA';

    public const ESTADO_COMPLETADA_CON_ERRORES = 'COMPLETADA_CON_ERRORES';

    public const ESTADO_FALLIDA = 'FALLIDA';

    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'tbl_sincronizacion_saf';

    /**
     * Llave primaria del modelo.
     */
    protected $primaryKey = 'id_sincronizacion_saf';

    /**
     * Laravel administrará created_at y updated_at.
     */
    public $timestamps = true;

    /**
     * Campos que pueden asignarse masivamente.
     */
    protected $fillable = [
        'uuid',
        'tipo_ejecucion',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'total_registros_recibidos',
        'total_registros_procesados',
        'total_registros_exitosos',
        'total_registros_con_error',
        'consultores_creados',
        'consultores_actualizados',
        'consultores_sin_cambios',
        'consultores_con_error',
        'capacitaciones_creadas',
        'capacitaciones_actualizadas',
        'capacitaciones_sin_cambios',
        'capacitaciones_desactivadas',
        'capacitaciones_con_error',
        'mensaje',
        'resumen',
        'usuario_ejecuta',
    ];

    /**
     * Conversiones automáticas de atributos.
     */
    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'resumen' => 'array',
        'total_registros_recibidos' => 'integer',
        'total_registros_procesados' => 'integer',
        'total_registros_exitosos' => 'integer',
        'total_registros_con_error' => 'integer',
        'consultores_creados' => 'integer',
        'consultores_actualizados' => 'integer',
        'consultores_sin_cambios' => 'integer',
        'consultores_con_error' => 'integer',
        'capacitaciones_creadas' => 'integer',
        'capacitaciones_actualizadas' => 'integer',
        'capacitaciones_sin_cambios' => 'integer',
        'capacitaciones_desactivadas' => 'integer',
        'capacitaciones_con_error' => 'integer',
    ];

    /**
     * Genera automáticamente el UUID antes de crear el registro.
     */
    protected static function booted(): void
    {
        static::creating(function (SincronizacionSaf $sincronizacion): void {
            if (blank($sincronizacion->uuid)) {
                $sincronizacion->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Usuario que ejecutó la sincronización.
     */
    public function usuarioEjecutor(): BelongsTo
    {
        return $this->belongsTo(
            Usuario::class,
            'usuario_ejecuta',
            'id_usuario'
        );
    }

    /**
     * Filtra sincronizaciones manuales.
     */
    public function scopeManuales(Builder $query): Builder
    {
        return $query->where(
            'tipo_ejecucion',
            self::TIPO_MANUAL
        );
    }

    /**
     * Filtra sincronizaciones automáticas.
     */
    public function scopeAutomaticas(Builder $query): Builder
    {
        return $query->where(
            'tipo_ejecucion',
            self::TIPO_AUTOMATICA
        );
    }

    /**
     * Filtra sincronizaciones completadas correctamente.
     */
    public function scopeCompletadas(Builder $query): Builder
    {
        return $query->where(
            'estado',
            self::ESTADO_COMPLETADA
        );
    }

    /**
     * Filtra sincronizaciones que presentaron algún tipo de error.
     */
    public function scopeConErrores(Builder $query): Builder
    {
        return $query->whereIn('estado', [
            self::ESTADO_COMPLETADA_CON_ERRORES,
            self::ESTADO_FALLIDA,
        ]);
    }

    /**
     * Determina si la sincronización está pendiente.
     */
    public function estaPendiente(): bool
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    /**
     * Determina si la sincronización está en proceso.
     */
    public function estaEnProceso(): bool
    {
        return $this->estado === self::ESTADO_EN_PROCESO;
    }

    /**
     * Determina si la sincronización finalizó correctamente.
     */
    public function fueCompletada(): bool
    {
        return $this->estado === self::ESTADO_COMPLETADA;
    }

    /**
     * Determina si la sincronización terminó con errores parciales.
     */
    public function fueCompletadaConErrores(): bool
    {
        return $this->estado === self::ESTADO_COMPLETADA_CON_ERRORES;
    }

    /**
     * Determina si la sincronización falló.
     */
    public function fallo(): bool
    {
        return $this->estado === self::ESTADO_FALLIDA;
    }

    /**
     * Determina si la sincronización ya terminó.
     */
    public function finalizo(): bool
    {
        return in_array($this->estado, [
            self::ESTADO_COMPLETADA,
            self::ESTADO_COMPLETADA_CON_ERRORES,
            self::ESTADO_FALLIDA,
        ], true);
    }

    /**
     * Determina si la ejecución fue manual.
     */
    public function esManual(): bool
    {
        return $this->tipo_ejecucion === self::TIPO_MANUAL;
    }

    /**
     * Determina si la ejecución fue automática.
     */
    public function esAutomatica(): bool
    {
        return $this->tipo_ejecucion === self::TIPO_AUTOMATICA;
    }

    /**
     * Determina si la ejecución registró errores.
     */
    public function tieneErrores(): bool
    {
        return $this->total_registros_con_error > 0
            || $this->consultores_con_error > 0
            || $this->capacitaciones_con_error > 0
            || $this->fueCompletadaConErrores()
            || $this->fallo();
    }

    /**
     * Calcula la duración en segundos.
     */
    public function duracionEnSegundos(): ?int
    {
        if ($this->fecha_inicio === null || $this->fecha_fin === null) {
            return null;
        }

        return (int) $this->fecha_inicio->diffInSeconds(
            $this->fecha_fin
        );
    }
}