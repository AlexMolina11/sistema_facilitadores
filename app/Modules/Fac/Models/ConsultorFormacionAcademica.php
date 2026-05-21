<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorFormacionAcademica extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_formacion_academica';
    protected $primaryKey = 'id_atestado';

    protected $fillable = [
        'id_consultor',
        'id_tipo_atestado',
        'id_nivel_academico',
        'id_pais',
        'descripcion',
        'institucion',
        'fecha_inicio',
        'fecha_fin',
        'url',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];
}