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
        'sexo',
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

    public function documentos()
    {
        return $this->hasMany(
            ConsultorDocumento::class,
            'id_consultor',
            'id_consultor'
        );
    }

}