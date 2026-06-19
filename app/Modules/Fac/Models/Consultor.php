<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultor extends Model
{
    use SoftDeletes;

    protected $table = 'tbl_consultor';

    protected $primaryKey = 'id_consultor';

    public $timestamps = true;

    protected $fillable = [
        'nombres',
        'apellidos',
        'apellido_casa',
        'estado_civil',
        'nacionalidad',
        'tipo_identificacion',
        'numero_identificacion',
        'nit',
        'nrc',
        'id_sexo',
        'fecha_nacimiento',
        'id_pais',
        'id_municipio',
        'direccion_residencia',
        'ruta_foto',
        'emergencia_contacto',
        'vigente',
        'id_instructor',
        'id_entidad',
        'activo',
        'usuario_crea',
        'usuario_mod',
        'usuario_elim',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'vigente' => 'boolean',
        'activo' => 'boolean',
    ];

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombres . ' ' . $this->apellidos);
    }

    public function emails()
    {
        return $this->hasMany(ConsultorEmail::class, 'id_consultor', 'id_consultor');
    }

    public function telefonos()
    {
        return $this->hasMany(ConsultorTelefono::class, 'id_consultor', 'id_consultor');
    }

    public function redesSociales()
    {
        return $this->hasMany(ConsultorRedSocial::class, 'id_consultor', 'id_consultor');
    }

    public function emergencias()
    {
        return $this->hasMany(ConsultorEmergencia::class, 'id_consultor', 'id_consultor');
    }

    public function formaciones()
    {
        return $this->hasMany(
            ConsultorFormacionAcademica::class,
            'id_consultor',
            'id_consultor'
        );
    }



    public function atestados()
    {
        return $this->hasMany(
            ConsultorAtestado::class,
            'id_consultor',
            'id_consultor'
        );
    }

    public function capacitacionesFepade()
    {
        return $this->hasMany(
            ConsultorCapacitacionFepade::class,
            'id_consultor',
            'id_consultor'
        );
    }

    public function habilidadesAtestados()
    {
        return $this->hasMany(
            ConsultorHabilidadAtestado::class,
            'id_consultor',
            'id_consultor'
        );
    }

    public function habilidadesCapacitaciones()
    {
        return $this->hasMany(
            ConsultorHabilidadCapacitacion::class,
            'id_consultor',
            'id_consultor'
        );
    }

    public function documentos()
    {
        return $this->hasMany(
            ConsultorDocumento::class,
            'id_consultor',
            'id_consultor'
        );
    }

    public function experienciasLaborales()
    {
        return $this->hasMany(
            ConsultorExperienciaLaboral::class,
            'id_consultor',
            'id_consultor'
        );
    }

    public function disponibilidades()
    {
        return $this->hasMany(
            ConsultorDisponibilidad::class,
            'id_consultor',
            'id_consultor'
        );
    }

    public function areasEspecializacion()
    {
        return $this->hasMany(
            ConsultorAreaEspecializacion::class,
            'id_consultor',
            'id_consultor'
        );
    }

    public function areasHabilidades()
    {
        return $this->hasManyThrough(
            ConsultorAreaHabilidad::class,
            ConsultorAreaEspecializacion::class,
            'id_consultor',
            'id_consultor_area',
            'id_consultor',
            'id_consultor_area'
        );
    }

    public function idiomas()
    {
        return $this->hasMany(
            ConsultorIdioma::class,
            'id_consultor',
            'id_consultor'
        );
    }

    public function tiposConsultoria()
    {
        return $this->hasMany(
            ConsultorTipoConsultoria::class,
            'id_consultor',
            'id_consultor'
        );
    }

    public function referencias()
    {
        return $this->hasMany(
            ConsultorReferencia::class,
            'id_consultor',
            'id_consultor'
        );
    }

    public function sexoCatalogo()
    {
        return $this->belongsTo(Sexo::class, 'id_sexo', 'id_sexo');
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'id_pais', 'id_pais');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio', 'id_municipio');
    }

}