<?php

namespace App\Modules\Fac\Models;

use App\Modules\Seg\Models\Usuario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SincronizacionSafError extends Model
{
    public const TIPO_REGISTRO_GENERAL = 'GENERAL';
    public const TIPO_REGISTRO_CONSULTOR = 'CONSULTOR';
    public const TIPO_REGISTRO_CAPACITACION = 'CAPACITACION';

    public const OPERACION_CONSULTAR = 'CONSULTAR';
    public const OPERACION_CREAR = 'CREAR';
    public const OPERACION_ACTUALIZAR = 'ACTUALIZAR';
    public const OPERACION_DESACTIVAR = 'DESACTIVAR';
    public const OPERACION_VALIDAR = 'VALIDAR';
    public const OPERACION_PROCESAR = 'PROCESAR';

    protected $table =
        'tbl_sincronizacion_saf_error';

    protected $primaryKey =
        'id_sincronizacion_saf_error';

    public $timestamps = true;

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

    protected $casts = [
        'id_sincronizacion_saf' => 'integer',
        'id_registro_local' => 'integer',
        'linea' => 'integer',
        'datos_recibidos' => 'array',
        'resuelto' => 'boolean',
        'fecha_resolucion' => 'datetime',
        'usuario_resuelve' => 'integer',
    ];

    public function sincronizacion(): BelongsTo
    {
        return $this->belongsTo(
            SincronizacionSaf::class,
            'id_sincronizacion_saf',
            'id_sincronizacion_saf'
        );
    }

    public function usuarioResolutor(): BelongsTo
    {
        return $this->belongsTo(
            Usuario::class,
            'usuario_resuelve',
            'id_usuario'
        );
    }

    public function scopePendientes(
        Builder $query
    ): Builder {
        return $query->where(
            'resuelto',
            false
        );
    }

    public function scopeResueltos(
        Builder $query
    ): Builder {
        return $query->where(
            'resuelto',
            true
        );
    }

    public function scopeDeConsultores(
        Builder $query
    ): Builder {
        return $query->where(
            'tipo_registro',
            self::TIPO_REGISTRO_CONSULTOR
        );
    }

    public function scopeDeCapacitaciones(
        Builder $query
    ): Builder {
        return $query->where(
            'tipo_registro',
            self::TIPO_REGISTRO_CAPACITACION
        );
    }

    public function scopeGenerales(
        Builder $query
    ): Builder {
        return $query->where(
            'tipo_registro',
            self::TIPO_REGISTRO_GENERAL
        );
    }

    public function estaPendiente(): bool
    {
        return $this->resuelto === false;
    }

    public function fueResuelto(): bool
    {
        return $this->resuelto === true;
    }

    public function esDeConsultor(): bool
    {
        return $this->tipo_registro
            === self::TIPO_REGISTRO_CONSULTOR;
    }

    public function esDeCapacitacion(): bool
    {
        return $this->tipo_registro
            === self::TIPO_REGISTRO_CAPACITACION;
    }

    public function esGeneral(): bool
    {
        return $this->tipo_registro
            === self::TIPO_REGISTRO_GENERAL;
    }

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

    public function reabrir(): bool
    {
        return $this->update([
            'resuelto' => false,
            'fecha_resolucion' => null,
            'usuario_resuelve' => null,
            'observacion_resolucion' => null,
        ]);
    }

    /**
     * Devuelve una recomendación funcional para resolver
     * el error sin permitir editar datos SAF desde Laravel.
     */
    public function recomendacionResolucion(): string
    {
        $codigo = Str::upper(
            trim((string) $this->codigo_error)
        );

        if ($codigo === 'ENTIDAD_NO_PERMITIDA') {
            return
                'Verificar en SAF que el instructor pertenezca '
                . 'a la entidad configurada para FEPADE. '
                . 'Después de corregir el dato de origen, SAF debe '
                . 'actualizar nuevamente el registro de importación.';
        }

        if ($codigo === 'CONSULTOR_NO_ENCONTRADO') {
            return
                'El instructor relacionado todavía no existe en '
                . 'Facilitadores. Verificar que el instructor haya '
                . 'sido enviado correctamente desde SAF y procesarlo '
                . 'antes de volver a procesar esta capacitación.';
        }

        if (Str::startsWith($codigo, 'VALIDACION_')) {
            return
                'El registro contiene un dato que no cumple las '
                . 'validaciones del sistema. Revisar el campo indicado '
                . 'y corregirlo en SAF. Después de la corrección, el '
                . 'registro debe volver a enviarse para procesamiento.';
        }

        if ($this->tipo_operacion === self::OPERACION_VALIDAR) {
            return
                'Revisar los datos recibidos desde SAF y corregir el '
                . 'valor inválido en el sistema de origen. Facilitadores '
                . 'no debe modificar directamente este dato.';
        }

        if ($this->tipo_operacion === self::OPERACION_PROCESAR) {
            return
                'Revisar el mensaje y el detalle técnico. Si el problema '
                . 'corresponde a datos provenientes de SAF, corregirlos '
                . 'en el origen y reenviar el registro. Si corresponde '
                . 'a un error interno de Laravel o base de datos, debe '
                . 'ser revisado por soporte técnico antes de reprocesar.';
        }

        return
            'Revisar el mensaje registrado, corregir la causa en el '
            . 'sistema correspondiente y volver a procesar el registro. '
            . 'No modificar directamente los datos SAF desde esta bitácora.';
    }
}