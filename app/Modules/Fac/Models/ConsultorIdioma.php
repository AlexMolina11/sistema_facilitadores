<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorIdioma extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_idioma';
    protected $primaryKey = 'id_consultor_idioma';

    protected $fillable = [
        'id_consultor',
        'id_idioma',
        'id_idioma_nivel',
        'url_certificado',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
        'deleted_at',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function idioma()
    {
        return $this->belongsTo(Idioma::class, 'id_idioma', 'id_idioma');
    }

    public function nivel()
    {
        return $this->belongsTo(IdiomaNivel::class, 'id_idioma_nivel', 'id_idioma_nivel');
    }
}
