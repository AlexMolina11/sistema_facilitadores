<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorHabilidadCapacitacion extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_habilidad_capacitacion';

    protected $primaryKey = 'id_habilidad_capacitacion';

    public $timestamps = true;

    protected $fillable = [
        'id_consultor',
        'id_habilidad',
        'id_capacitacion_fepade',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function consultor()
    {
        return $this->belongsTo(Consultor::class, 'id_consultor', 'id_consultor');
    }

    public function habilidad()
    {
        return $this->belongsTo(Habilidad::class, 'id_habilidad', 'id_habilidad');
    }

    public function capacitacionFepade()
    {
        return $this->belongsTo(ConsultorCapacitacionFepade::class, 'id_capacitacion_fepade', 'id_capacitacion_fepade');
    }
}
