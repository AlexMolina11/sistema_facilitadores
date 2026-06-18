<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorAtestado extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_atestado';

    protected $primaryKey = 'id_atestado';

    public $timestamps = true;

    protected $fillable = [
        'id_consultor',
        'id_tipo_formacion',
        'id_tipo_atestado',
        'id_nivel_academico',
        'id_pais',
        'titulo',
        'descripcion',
        'institucion',
        'entidad_acreditadora',
        'cliente_institucion',
        'codigo_acreditacion',
        'fecha_inicio',
        'fecha_fin',
        'fecha_emision',
        'fecha_vencimiento',
        'horas',
        'url_archivo',
        'nombre_archivo_original',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'horas' => 'integer',
        'activo' => 'boolean',
    ];

    public function consultor()
    {
        return $this->belongsTo(Consultor::class, 'id_consultor', 'id_consultor');
    }

    public function tipoFormacion()
    {
        return $this->belongsTo(TipoFormacion::class, 'id_tipo_formacion', 'id_tipo_formacion');
    }

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

    public function habilidadesAtestado()
    {
        return $this->hasMany(ConsultorHabilidadAtestado::class, 'id_atestado', 'id_atestado');
    }
}
