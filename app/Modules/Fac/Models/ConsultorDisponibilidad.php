<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorDisponibilidad extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_disponibilidad';
    protected $primaryKey = 'id_consultor_disponibilidad';

    protected $fillable = [
        'id_consultor',
        'id_tipo_disponibilidad',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
        'deleted_at',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function tipoDisponibilidad()
    {
        return $this->belongsTo(TipoDisponibilidad::class, 'id_tipo_disponibilidad', 'id_tipo_disponibilidad');
    }
}
