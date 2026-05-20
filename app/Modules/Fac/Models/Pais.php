<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pais extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_pais';
    protected $primaryKey = 'id_pais';
    public $timestamps = true;

     public function getRouteKeyName(): string
    {
        return 'id_pais';
    }

    protected $fillable = [
        'codigo_pais',
        'nombre_pais',
        'mh_codigo_pais',
        'mh_codigo_pais_new',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}