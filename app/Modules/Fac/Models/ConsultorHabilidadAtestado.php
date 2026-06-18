<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultorHabilidadAtestado extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor_habilidad_atestado';

    protected $primaryKey = 'id_habilidad_atestado';

    public $timestamps = true;

    protected $fillable = [
        'id_consultor',
        'id_habilidad',
        'id_atestado',
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
        return $this->belongsTo(Consultor::class, 'id_consultor', 'id_consultor');
    }

    public function habilidad()
    {
        return $this->belongsTo(Habilidad::class, 'id_habilidad', 'id_habilidad');
    }

    public function atestado()
    {
        return $this->belongsTo(ConsultorAtestado::class, 'id_atestado', 'id_atestado');
    }
}
