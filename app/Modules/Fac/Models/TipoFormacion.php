<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoFormacion extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_tipo_formacion';

    protected $primaryKey = 'id_tipo_formacion';

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