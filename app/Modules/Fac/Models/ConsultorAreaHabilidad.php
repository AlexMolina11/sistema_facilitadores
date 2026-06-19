<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorAreaHabilidad extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_area_habilidad';
    protected $primaryKey = 'id_consultor_area_habilidad';
    public $timestamps = true;

    protected $fillable = [
        'id_consultor_area',
        'id_habilidad_tecnica',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function consultorArea()
    {
        return $this->belongsTo(ConsultorAreaEspecializacion::class, 'id_consultor_area', 'id_consultor_area');
    }

    public function habilidadTecnica()
    {
        return $this->belongsTo(HabilidadTecnica::class, 'id_habilidad_tecnica', 'id_habilidad_tecnica');
    }
}
