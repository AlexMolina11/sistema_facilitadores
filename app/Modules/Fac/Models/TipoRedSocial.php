<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoRedSocial extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_tipo_red_social';
    protected $primaryKey = 'id_tipo_red_social';

    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'icono',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}