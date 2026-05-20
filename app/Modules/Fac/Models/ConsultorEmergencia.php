<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorEmergencia extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_emergencia';
    protected $primaryKey = 'id_consultor_emergencia';

    protected $fillable = [
        'id_consultor',
        'nombre',
        'telefono',
        'correo',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}