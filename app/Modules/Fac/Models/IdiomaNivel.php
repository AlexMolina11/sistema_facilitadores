<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdiomaNivel extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_idioma_nivel';

    protected $primaryKey = 'id_idioma_nivel';

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