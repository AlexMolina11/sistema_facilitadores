<?php

namespace App\Modules\Fac\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Fac\Models\TipoReferencia;

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

    public function avancePerfil(): array
    {
        $puntos = [];
        $puntosDinamicos = [];

        $atestados = $this->atestados()
            ->where('activo', true)
            ->whereNull('deleted_at')
            ->get();

        $capacitacionesFepade = $this->capacitacionesFepade()
            ->where('activo', true)
            ->whereNull('deleted_at')
            ->get();

        $areasEspecializacion = $this->areasEspecializacion()
            ->where('activo', true)
            ->whereNull('deleted_at')
            ->get();

        $puntos['Datos personales obligatorios'] =
            filled($this->nombres)
            && filled($this->apellidos)
            && filled($this->id_sexo)
            && filled($this->fecha_nacimiento)
            && filled($this->nacionalidad);

        $puntos['Documento de identidad con archivo'] =
            $this->documentos()
                ->where('activo', true)
                ->whereNull('deleted_at')
                ->whereNotNull('numero')
                ->whereNotNull('url_archivo')
                ->exists();

        $puntos['Residencia completa'] =
            filled($this->id_pais)
            && filled($this->id_municipio)
            && filled($this->direccion_residencia);

        $puntos['Correo principal'] =
            $this->emails()
                ->where('activo', true)
                ->whereNull('deleted_at')
                ->where('principal', true)
                ->exists();

        $puntos['Teléfono registrado'] =
            $this->telefonos()
                ->where('activo', true)
                ->whereNull('deleted_at')
                ->exists();

        $puntos['Contacto de emergencia'] =
            $this->emergencias()
                ->where('activo', true)
                ->whereNull('deleted_at')
                ->exists();

        $puntos['Experiencia laboral'] =
            $this->experienciasLaborales()
                ->where('activo', true)
                ->whereNull('deleted_at')
                ->exists();

        $puntos['Educación formal'] =
            $this->atestados()
                ->where('activo', true)
                ->whereNull('deleted_at')
                ->whereHas('tipoFormacion', function ($query) {
                    $query->where('nombre', 'like', '%formal%');
                })
                ->exists();

        $puntos['Educación continua'] =
            $this->atestados()
                ->where('activo', true)
                ->whereNull('deleted_at')
                ->whereHas('tipoFormacion', function ($query) {
                    $query->where('nombre', 'like', '%continua%');
                })
                ->exists();

        $puntos['Área de especialización con evidencia'] =
            $this->areasEspecializacion()
                ->where('activo', true)
                ->whereNull('deleted_at')
                ->where(function ($query) {
                    $query->whereNotNull('id_atestado')
                        ->orWhereNotNull('id_capacitacion_fepade');
                })
                ->exists();

        foreach ($atestados as $atestado) {
            $puntosDinamicos["Área vinculada al atestado: {$atestado->titulo}"] =
                $areasEspecializacion
                    ->where('id_atestado', $atestado->id_atestado)
                    ->isNotEmpty();
        }

        foreach ($capacitacionesFepade as $capacitacion) {
            $puntosDinamicos["Área vinculada a capacitación FEPADE: {$capacitacion->nombre_evento}"] =
                $areasEspecializacion
                    ->where('id_capacitacion_fepade', $capacitacion->id_capacitacion_fepade)
                    ->isNotEmpty();
        }

        $puntos['Idioma registrado'] =
            $this->idiomas()
                ->where('activo', true)
                ->whereNull('deleted_at')
                ->exists();

        $tiposReferencia = TipoReferencia::query()
            ->where('activo', true)
            ->whereNull('deleted_at')
            ->get();

        foreach ($tiposReferencia as $tipoReferencia) {
            $puntos["Referencia {$tipoReferencia->nombre}"] =
                $this->referencias()
                    ->where('activo', true)
                    ->whereNull('deleted_at')
                    ->where('id_tipo_referencia', $tipoReferencia->id_tipo_referencia)
                    ->exists();
        }

        $puntos['Disponibilidad actual'] =
            $this->disponibilidades()
                ->where('activo', true)
                ->whereNull('deleted_at')
                ->exists();

        $todosLosPuntos = array_merge($puntos, $puntosDinamicos);

        $total = count($todosLosPuntos);
        $obtenidos = collect($todosLosPuntos)->filter()->count();
        $porcentaje = $total > 0 ? round(($obtenidos / $total) * 100) : 0;

        return [
            'porcentaje' => $porcentaje,
            'obtenidos' => $obtenidos,
            'total' => $total,
            'puntos_fijos' => $puntos,
            'puntos_dinamicos' => $puntosDinamicos,
            'todos' => $todosLosPuntos,
        ];
    }

}