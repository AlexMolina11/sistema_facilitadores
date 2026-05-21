<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorRedSocial extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_red_social';
    protected $primaryKey = 'id_red';

    protected $fillable = [
        'id_consultor',
        'id_tipo_red_social',
        'enlace',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}