<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorFormacionAcademica extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_formacion_academica';
    protected $primaryKey = 'id_atestado';

    protected $fillable = [
        'id_consultor',
        'id_tipo_atestado',
        'id_nivel_academico',
        'id_pais',
        'descripcion',
        'institucion',
        'fecha_inicio',
        'fecha_fin',
        'url',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    public function tipoAtestado()
    {
        return $this->belongsTo(TipoAtestado::class, 'id_tipo_atestado', 'id_tipo_atestado');
    }

    public function nivelAcademico()
    {
        return $this->belongsTo(NivelAcademico::class, 'id_nivel_academico', 'id_nivel_academico');
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'id_pais', 'id_pais');
    }
}
