<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HabilidadTecnica extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_habilidad_tecnica';
    protected $primaryKey = 'id_habilidad_tecnica';
    public $timestamps = true;

    protected $fillable = [
        'id_area_especializacion',
        'nombre',
        'descripcion',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function areaEspecializacion()
    {
        return $this->belongsTo(AreaEspecializacion::class, 'id_area_especializacion', 'id_area_especializacion');
    }
}
