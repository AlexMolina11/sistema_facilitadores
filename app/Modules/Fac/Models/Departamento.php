<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
    use SoftDeletes;

    protected $table      = 'tbl_departamento';
    protected $primaryKey = 'id_departamento';
    public $timestamps    = true;

    protected $fillable = [
        'id_pais',
        'nombre_departamento',
        'mh_codigo_depto',
        'georeferencia',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'id_pais', 'id_pais');
    }
}