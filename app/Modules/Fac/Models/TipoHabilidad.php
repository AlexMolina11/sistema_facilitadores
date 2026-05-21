<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoHabilidad extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_tipo_habilidad';
    protected $primaryKey = 'id_tipo_habilidad';

    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}