<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorHabilidad extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_habilidad';
    protected $primaryKey = 'id_consultor_habilidad';

    protected $fillable = [
        'id_consultor',
        'id_habilidad',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}