<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoTelefono extends Model
{
    use SoftDeletes;

    protected $table      = 'tbl_tipo_telefono';
    protected $primaryKey = 'id_tipo_telefono';
    public $timestamps    = true;

    protected $fillable = [
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}