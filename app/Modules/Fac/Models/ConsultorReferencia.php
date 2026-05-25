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
        'id_tipo_relacion',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function tipoRelacion()
    {
        return $this->belongsTo(TipoRelacion::class, 'id_tipo_relacion', 'id_tipo_relacion');
    }

    public function tipoReferencia()
    {
        return $this->belongsTo(TipoReferencia::class, 'id_tipo_referencia', 'id_tipo_referencia');
    }
}
