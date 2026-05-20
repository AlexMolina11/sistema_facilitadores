<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorEmail extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_email';
    protected $primaryKey = 'id_email';

    protected $fillable = [
        'id_consultor',
        'email',
        'principal',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'principal' => 'boolean',
        'activo' => 'boolean',
    ];
}