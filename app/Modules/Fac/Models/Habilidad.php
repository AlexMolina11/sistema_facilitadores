<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Habilidad extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_habilidad';

    protected $primaryKey = 'id_habilidad';

    public $timestamps = true;

    protected $fillable = [
        'id_tipo_habilidad',
        'nombre',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function tipoHabilidad()
    {
        return $this->belongsTo(
            TipoHabilidad::class,
            'id_tipo_habilidad',
            'id_tipo_habilidad'
        );
    }
}