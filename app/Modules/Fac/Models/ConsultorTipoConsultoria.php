<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorTipoConsultoria extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_tipo_consultoria';
    protected $primaryKey = 'id_consultor_tipo_consultoria';

    protected $fillable = [
        'id_consultor',
        'id_tipo_consultoria',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}