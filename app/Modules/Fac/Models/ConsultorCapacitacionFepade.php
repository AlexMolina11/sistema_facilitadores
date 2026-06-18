<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorCapacitacionFepade extends Model
{
    use SoftDeletes;

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
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'horas' => 'integer',
        'activo' => 'boolean',
    ];

    public function consultor()
    {
        return $this->belongsTo(Consultor::class, 'id_consultor', 'id_consultor');
    }

    public function habilidadesCapacitacion()
    {
        return $this->hasMany(ConsultorHabilidadCapacitacion::class, 'id_capacitacion_fepade', 'id_capacitacion_fepade');
    }
}
