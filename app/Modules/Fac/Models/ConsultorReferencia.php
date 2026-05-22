<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorReferencia extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_referencia';
    protected $primaryKey = 'id_referencia';

    protected $fillable = [
        'id_consultor',
        'id_tipo_referencia',
        'nombre',
        'telefono',
        'correo',
        'empresa',
        'cargo',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}