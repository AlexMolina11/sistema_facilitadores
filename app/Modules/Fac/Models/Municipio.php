<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Municipio extends Model
{
    use SoftDeletes;

    protected $table      = 'tbl_municipio';
    protected $primaryKey = 'id_municipio';
    public $timestamps    = true;

    protected $fillable = [
        'id_pais',
        'id_departamento',
        'id_municipio_mh',
        'nombre_distrito',
        'mh_codigo_distrito',
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

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento', 'id_departamento');
    }

    public function municipioMh()
    {
        return $this->belongsTo(MunicipioMh::class, 'id_municipio_mh', 'id_municipio_mh');
    }
}