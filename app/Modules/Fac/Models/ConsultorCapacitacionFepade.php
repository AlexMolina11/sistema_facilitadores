<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorCapacitacionFepade extends Model
{
    use SoftDeletes;

    public const FUENTE_FEPADE = 'FEPADE';

    public const FUENTE_SAF = 'SAF';

    protected $table = 'tbl_consultor_capacitacion_fepade';

    protected $primaryKey = 'id_capacitacion_fepade';

    public $timestamps = true;

    protected $fillable = [
        'id_consultor',

        /*
        |--------------------------------------------------------------------------
        | Información de la capacitación
        |--------------------------------------------------------------------------
        */
        'programa_curso_id',
        'codigo_evento',
        'curso_nombre',
        'fecha_inicio',
        'fecha_fin',
        'estado_curso_nombre',
        'no_horas_real',
        'modalidad',
        'tipo_evento_nombre',
        'cliente',
        'encuesta_id',
        'encuesta_nombre',
        'promedio_encuesta',
        'fecha_evaluacion',

        /*
        |--------------------------------------------------------------------------
        | Información interna
        |--------------------------------------------------------------------------
        */
        'fuente',
        'fecha_ultima_sincronizacion_saf',
        'hash_datos_saf',
        'activo',

        /*
        |--------------------------------------------------------------------------
        | Auditoría
        |--------------------------------------------------------------------------
        */
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'programa_curso_id' => 'integer',

        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',

        'no_horas_real' => 'integer',

        'encuesta_id' => 'integer',
        'promedio_encuesta' => 'decimal:2',
        'fecha_evaluacion' => 'datetime',

        'fecha_ultima_sincronizacion_saf' => 'datetime',

        'activo' => 'boolean',
    ];

    public function consultor()
    {
        return $this->belongsTo(
            Consultor::class,
            'id_consultor',
            'id_consultor'
        );
    }

    public function areasEspecializacion()
    {
        return $this->hasMany(
            ConsultorAreaEspecializacion::class,
            'id_capacitacion_fepade',
            'id_capacitacion_fepade'
        );
    }

    /**
     * Determina si la capacitación proviene del sistema SAF.
     */
    public function provieneDeSaf(): bool
    {
        return $this->fuente === self::FUENTE_SAF;
    }

    /**
     * Determina si la capacitación pertenece a la fuente FEPADE.
     */
    public function provieneDeFepade(): bool
    {
        return $this->fuente === self::FUENTE_FEPADE;
    }

    /**
     * Determina si la capacitación posee código de evento.
     */
    public function tieneCodigoEvento(): bool
    {
        return filled($this->codigo_evento);
    }

    /**
     * Alias temporal para compatibilidad con código existente.
     *
     * Se podrá eliminar cuando todas las referencias antiguas
     * hayan sido migradas.
     */
    public function tieneCodigoEventoExterno(): bool
    {
        return $this->tieneCodigoEvento();
    }

    /**
     * Determina si la capacitación ya fue sincronizada desde SAF.
     */
    public function fueSincronizadaConSaf(): bool
    {
        return $this->fecha_ultima_sincronizacion_saf !== null;
    }
}