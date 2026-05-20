<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorTelefono extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_telefono';
    protected $primaryKey = 'id_consultor_telefono';

    protected $fillable = [
        'id_consultor',
        'id_tipo_telefono',
        'numero_telefono',
        'extension',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}