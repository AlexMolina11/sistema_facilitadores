<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Idioma extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_idioma';

    protected $primaryKey = 'id_idioma';

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