<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoAtestado extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_tipo_atestado';

    protected $primaryKey = 'id_tipo_atestado';

    public $timestamps = true;

    protected $fillable = [
        'id_tipo_formacion',
        'nombre',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function tipoFormacion()
    {
        return $this->belongsTo(
            TipoFormacion::class,
            'id_tipo_formacion',
            'id_tipo_formacion'
        );
    }
}