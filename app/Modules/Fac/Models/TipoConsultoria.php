<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoConsultoria extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_tipo_consultoria';

    protected $primaryKey = 'id_tipo_consultoria';

    public $timestamps = true;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'nombre',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}