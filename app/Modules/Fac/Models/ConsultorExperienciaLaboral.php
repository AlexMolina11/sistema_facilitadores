<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorExperienciaLaboral extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_experiencia_laboral';
    protected $primaryKey = 'id_experiencia';

    protected $fillable = [
        'id_consultor',
        'empresa',
        'cargo',
        'descripcion',
        'desde',
        'hasta',
        'trabajo_actual',
        'jefe_nombre',
        'jefe_email',
        'jefe_telefono',
        'url_evidencia',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'desde' => 'date',
        'hasta' => 'date',
        'trabajo_actual' => 'boolean',
        'activo' => 'boolean',
    ];
}