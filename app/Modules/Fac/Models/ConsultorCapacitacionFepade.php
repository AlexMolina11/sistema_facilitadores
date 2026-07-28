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
        'codigo_evento_externo',
        'nombre_evento',
        'tema',
        'institucion',
        'modalidad',
        'fecha_inicio',
        'fecha_fin',
        'horas',
        'fuente',
        'fecha_ultima_sincronizacion_saf',
        'hash_datos_saf',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'horas' => 'integer',
        'fecha_ultima_sincronizacion_saf' => 'datetime',
        'activo' => 'boolean',
    ];

    public function consultor()
    {
        return $this->belongsTo(Consultor::class, 'id_consultor', 'id_consultor');
    }

    public function areasEspecializacion()
    {
        return $this->hasMany(ConsultorAreaEspecializacion::class, 'id_capacitacion_fepade', 'id_capacitacion_fepade');
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
     * Determina si la capacitación posee un código externo.
     */
    public function tieneCodigoEventoExterno(): bool
    {
        return filled($this->codigo_evento_externo);
    }

    /**
     * Determina si la capacitación ya fue sincronizada desde SAF.
     */
    public function fueSincronizadaConSaf(): bool
    {
        return $this->fecha_ultima_sincronizacion_saf !== null;
    }
}
