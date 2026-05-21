<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MunicipioMh extends Model
{
    use SoftDeletes;

    protected $table      = 'tbl_municipio_mh';
    protected $primaryKey = 'id_municipio_mh';
    public $timestamps    = true;

    protected $fillable = [
        'id_departamento',
        'municipio_mh_nombre',
        'mh_codigo_municipio',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento', 'id_departamento');
    }
}