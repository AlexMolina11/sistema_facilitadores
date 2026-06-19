<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaEspecializacion extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_area_especializacion';
    protected $primaryKey = 'id_area_especializacion';
    public $timestamps = true;

    protected $fillable = [
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

    public function habilidadesTecnicas()
    {
        return $this->hasMany(HabilidadTecnica::class, 'id_area_especializacion', 'id_area_especializacion');
    }
}
