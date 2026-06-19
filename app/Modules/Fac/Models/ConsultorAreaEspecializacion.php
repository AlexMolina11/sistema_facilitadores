<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorAreaEspecializacion extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_area_especializacion';
    protected $primaryKey = 'id_consultor_area';
    public $timestamps = true;

    protected $fillable = [
        'id_consultor',
        'id_area_especializacion',
        'id_atestado',
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

    public function areaEspecializacion()
    {
        return $this->belongsTo(AreaEspecializacion::class, 'id_area_especializacion', 'id_area_especializacion');
    }

    public function atestado()
    {
        return $this->belongsTo(ConsultorAtestado::class, 'id_atestado', 'id_atestado');
    }

    public function capacitacionFepade()
    {
        return $this->belongsTo(ConsultorCapacitacionFepade::class, 'id_capacitacion_fepade', 'id_capacitacion_fepade');
    }

    public function habilidades()
    {
        return $this->hasMany(ConsultorAreaHabilidad::class, 'id_consultor_area', 'id_consultor_area');
    }
}
