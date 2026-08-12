<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorDocumento extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_documento';

    protected $primaryKey = 'id_documento';

    protected $fillable = [
        'id_consultor',
        'id_tipo_documento',
        'numero',
        'actividad_giro',
        'url_archivo',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function consultor()
    {
        return $this->belongsTo(
            Consultor::class,
            'id_consultor',
            'id_consultor'
        );
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(
            TipoDocumento::class,
            'id_tipo_documento',
            'id_tipo_documento'
        );
    }
}