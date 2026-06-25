<?php

namespace App\Modules\Fac\Services;

use App\Modules\Fac\Models\Consultor;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class BusquedaConsultorService
{
    public function buscar(array $filtros): LengthAwarePaginator
    {
        $query = Consultor::query()
            ->with([
                'emails' => fn ($q) => $q->where('activo', true)->orderByDesc('principal'),
                'telefonos' => fn ($q) => $q->where('activo', true),
                'sexoCatalogo',
                'disponibilidades' => fn ($q) => $q->where('activo', true),
                'areasEspecializacion' => fn ($q) => $q
                    ->where('activo', true)
                    ->with([
                        'areaEspecializacion',
                        'habilidades' => fn ($h) => $h
                            ->where('activo', true)
                            ->with('habilidadTecnica'),
                    ]),
                'idiomas' => fn ($q) => $q->where('activo', true),
                'experienciasLaborales' => fn ($q) => $q->where('activo', true),
                'atestados' => fn ($q) => $q
                    ->where('activo', true)
                    ->with(['tipoFormacion', 'tipoAtestado', 'nivelAcademico', 'pais']),
            ])
            ->where('activo', true);

        $this->aplicarBusquedaGeneral($query, $filtros);
        $this->aplicarFechas($query, $filtros);
        $this->aplicarDatosGenerales($query, $filtros);
        $this->aplicarUbicacionYDisponibilidad($query, $filtros);
        $this->aplicarHabilidades($query, $filtros);
        $this->aplicarEducacion($query, $filtros);
        $this->aplicarIdiomas($query, $filtros);
        $this->aplicarExperiencia($query, $filtros);

        return $query
            ->latest('updated_at')
            ->paginate(12)
            ->withQueryString();
    }

    private function aplicarBusquedaGeneral(Builder $query, array $filtros): void
    {
        $texto = trim((string) Arr::get($filtros, 'q'));

        if ($texto === '') {
            return;
        }

        /**
         * Se separa la búsqueda por palabras para que una frase como
         * "juan excel san salvador" funcione como búsqueda acumulativa.
         * Cada palabra debe aparecer en algún campo aplicable del consultor
         * o de sus relaciones.
         */
        $terminos = collect(preg_split('/\s+/', $texto, -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($termino) => trim($termino))
            ->filter()
            ->take(8)
            ->values();

        foreach ($terminos as $termino) {
            $like = "%{$termino}%";

            $query->where(function (Builder $sub) use ($like) {
                $sub->where('nombres', 'like', $like)
                    ->orWhere('apellidos', 'like', $like)
                    ->orWhere(DB::raw("CONCAT(COALESCE(nombres, ''), ' ', COALESCE(apellidos, ''))"), 'like', $like)
                    ->orWhere('apellido_casa', 'like', $like)
                    ->orWhere('estado_civil', 'like', $like)
                    ->orWhere('nacionalidad', 'like', $like)
                    ->orWhere('tipo_identificacion', 'like', $like)
                    ->orWhere('numero_identificacion', 'like', $like)
                    ->orWhere('nit', 'like', $like)
                    ->orWhere('nrc', 'like', $like)
                    ->orWhere('direccion_residencia', 'like', $like)
                    ->orWhereHas('emails', fn (Builder $email) => $email
                        ->where('activo', true)
                        ->where('email', 'like', $like))
                    ->orWhereHas('telefonos', fn (Builder $telefono) => $telefono
                        ->where('activo', true)
                        ->where(function (Builder $t) use ($like) {
                            $t->where('numero_telefono', 'like', $like)
                                ->orWhere('extension', 'like', $like);
                        }))
                    ->orWhereHas('sexoCatalogo', fn (Builder $sexo) => $sexo->where('nombre', 'like', $like))
                    ->orWhereHas('pais', fn (Builder $pais) => $pais->where('nombre_pais', 'like', $like))
                    ->orWhereHas('municipio', function (Builder $municipio) use ($like) {
                        $municipio->where('nombre_distrito', 'like', $like)
                            ->orWhereHas('departamento', fn (Builder $depto) => $depto->where('nombre_departamento', 'like', $like))
                            ->orWhereHas('municipioMh', fn (Builder $mun) => $mun->where('municipio_mh_nombre', 'like', $like));
                    })
                    ->orWhereHas('experienciasLaborales', fn (Builder $exp) => $exp
                        ->where('activo', true)
                        ->where(function (Builder $e) use ($like) {
                            $e->where('empresa', 'like', $like)
                                ->orWhere('cargo', 'like', $like)
                                ->orWhere('descripcion', 'like', $like)
                                ->orWhere('jefe_nombre', 'like', $like);
                        }))
                    ->orWhereHas('atestados', fn (Builder $formacion) => $formacion
                        ->where('activo', true)
                        ->where(function (Builder $f) use ($like) {
                            $f->where('titulo', 'like', $like)
                                ->orWhere('descripcion', 'like', $like)
                                ->orWhere('institucion', 'like', $like)
                                ->orWhere('entidad_acreditadora', 'like', $like)
                                ->orWhere('cliente_institucion', 'like', $like)
                                ->orWhereHas('tipoFormacion', fn (Builder $tipo) => $tipo->where('nombre', 'like', $like))
                                ->orWhereHas('nivelAcademico', fn (Builder $nivel) => $nivel->where('nombre', 'like', $like))
                                ->orWhereHas('tipoAtestado', fn (Builder $atestado) => $atestado->where('nombre', 'like', $like))
                                ->orWhereHas('pais', fn (Builder $pais) => $pais->where('nombre_pais', 'like', $like));
                        }))
                    ->orWhereHas('areasEspecializacion', function (Builder $area) use ($like) {
                            $area->where('activo', true)
                                ->where(function (Builder $a) use ($like) {
                                    $a->whereHas('areaEspecializacion', fn (Builder $catalogo) => $catalogo->where('nombre', 'like', $like))
                                    ->orWhereHas('habilidades.habilidadTecnica', fn (Builder $hab) => $hab->where('nombre', 'like', $like));
                                });
                        })
                    ->orWhereHas('idiomas', fn (Builder $idioma) => $idioma
                        ->where('activo', true)
                        ->where(function (Builder $i) use ($like) {
                            $i->whereHas('idioma', fn (Builder $idiomaCatalogo) => $idiomaCatalogo->where('nombre', 'like', $like))
                                ->orWhereHas('nivel', fn (Builder $nivel) => $nivel->where('nombre', 'like', $like));
                        }))
                    ->orWhereHas('disponibilidades', fn (Builder $disp) => $disp
                        ->where('activo', true)
                        ->whereHas('tipoDisponibilidad', fn (Builder $tipo) => $tipo->where('nombre', 'like', $like)));
            });
        }
    }

    private function aplicarFechas(Builder $query, array $filtros): void
    {
        $columna = Arr::get($filtros, 'fecha_tipo') === 'creacion' ? 'created_at' : 'updated_at';
        [$desde, $hasta] = $this->resolverRangoFechas($filtros);

        if ($desde && $hasta) {
            $query->whereBetween($columna, [$desde->startOfDay(), $hasta->endOfDay()]);
        }
    }

    private function resolverRangoFechas(array $filtros): array
    {
        return match (Arr::get($filtros, 'fecha_filtro')) {
            'hoy' => [now(), now()],
            '7_dias' => [now()->subDays(7), now()],
            '30_dias' => [now()->subDays(30), now()],
            'este_mes' => [now()->startOfMonth(), now()->endOfMonth()],
            'mes_pasado' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
            'personalizado' => [
                Arr::get($filtros, 'fecha_desde') ? Carbon::parse($filtros['fecha_desde']) : null,
                Arr::get($filtros, 'fecha_hasta') ? Carbon::parse($filtros['fecha_hasta']) : null,
            ],
            default => [null, null],
        };
    }

    private function aplicarDatosGenerales(Builder $query, array $filtros): void
    {
        $query->when(Arr::get($filtros, 'sexo'), fn (Builder $q, $sexo) => $q->where('id_sexo', $sexo));

        if (Arr::get($filtros, 'edad_min')) {
            $fechaMaxima = now()->subYears((int) $filtros['edad_min'])->toDateString();
            $query->whereDate('fecha_nacimiento', '<=', $fechaMaxima);
        }

        if (Arr::get($filtros, 'edad_max')) {
            $fechaMinima = now()->subYears(((int) $filtros['edad_max']) + 1)->addDay()->toDateString();
            $query->whereDate('fecha_nacimiento', '>=', $fechaMinima);
        }
    }

    private function aplicarUbicacionYDisponibilidad(Builder $query, array $filtros): void
    {
        $query->when(Arr::get($filtros, 'pais'), fn (Builder $q, $pais) => $q->where('id_pais', $pais));
        $query->when(Arr::get($filtros, 'distrito'), fn (Builder $q, $distrito) => $q->where('id_municipio', $distrito));

        $query->when(Arr::get($filtros, 'departamento'), function (Builder $q, $departamento) {
            $q->whereHas('municipio', fn (Builder $m) => $m->where('id_departamento', $departamento));
        });

        $query->when(Arr::get($filtros, 'municipio_mh'), function (Builder $q, $municipioMh) {
            $q->whereHas('municipio', fn (Builder $m) => $m->where('id_municipio_mh', $municipioMh));
        });

        $query->when(Arr::get($filtros, 'disponibilidad'), function (Builder $q, $disponibilidad) {
            $q->whereHas('disponibilidades', function (Builder $d) use ($disponibilidad) {
                $d->where('activo', true)->where('id_tipo_disponibilidad', $disponibilidad);
            });
        });
    }

    private function aplicarHabilidades(Builder $query, array $filtros): void
    {
        $areas = array_filter((array) Arr::get($filtros, 'area_especializacion', []));
        $habilidadesTecnicas = array_filter((array) Arr::get($filtros, 'habilidades_tecnicas', []));

        if (!empty($areas)) {
            $query->whereHas('areasEspecializacion', function (Builder $area) use ($areas) {
                $area->where('activo', true)
                    ->whereNull('deleted_at')
                    ->whereIn('id_area_especializacion', $areas);
            });
        }

        if (!empty($habilidadesTecnicas)) {
            $query->whereHas('areasEspecializacion.habilidades', function (Builder $habilidad) use ($habilidadesTecnicas) {
                $habilidad->where('activo', true)
                    ->whereNull('deleted_at')
                    ->whereIn('id_habilidad_tecnica', $habilidadesTecnicas);
            });
        }
    }

    private function aplicarEducacion(Builder $query, array $filtros): void
    {
        $nivelesAcademicos = array_filter((array) Arr::get($filtros, 'nivel_academico', []));
        $tiposFormacion = array_filter((array) Arr::get($filtros, 'tipo_formacion', []));
        $tiposAtestado = array_filter((array) Arr::get($filtros, 'tipo_atestado', []));
        $paisEducacion = Arr::get($filtros, 'educacion_pais');
        $institucion = trim((string) Arr::get($filtros, 'educacion_institucion'));

        if (
            empty($nivelesAcademicos)
            && empty($tiposFormacion)
            && empty($tiposAtestado)
            && empty($paisEducacion)
            && $institucion === ''
        ) {
            return;
        }

        $query->whereHas('atestados', function (Builder $f) use (
            $nivelesAcademicos,
            $tiposFormacion,
            $tiposAtestado,
            $paisEducacion,
            $institucion
        ) {
            $f->where('activo', true)
                ->whereNull('deleted_at');

            if (!empty($nivelesAcademicos)) {
                $f->whereIn('id_nivel_academico', $nivelesAcademicos);
            }

            if (!empty($tiposFormacion)) {
                $f->whereIn('id_tipo_formacion', $tiposFormacion);
            }

            if (!empty($tiposAtestado)) {
                $f->whereIn('id_tipo_atestado', $tiposAtestado);
            }

            if (!empty($paisEducacion)) {
                $f->where('id_pais', $paisEducacion);
            }

            if ($institucion !== '') {
                $f->where(function (Builder $i) use ($institucion) {
                    $i->where('institucion', 'like', "%{$institucion}%")
                        ->orWhere('entidad_acreditadora', 'like', "%{$institucion}%")
                        ->orWhere('cliente_institucion', 'like', "%{$institucion}%");
                });
            }
        });
    }

    private function aplicarIdiomas(Builder $query, array $filtros): void
    {
        $idiomas = Arr::get($filtros, 'idiomas', []);

        if (empty($idiomas)) {
            return;
        }

        foreach ($idiomas as $idIdioma => $idNivel) {

            if (empty($idNivel)) {
                continue;
            }

            $query->whereHas('idiomas', function (Builder $i) use ($idIdioma, $idNivel) {

                $i->where('activo', true)
                ->where('id_idioma', $idIdioma)
                ->where('id_idioma_nivel', '>=', $idNivel);

            });
        }
    }

    private function aplicarExperiencia(Builder $query, array $filtros): void
    {
        $cargo = trim((string) Arr::get($filtros, 'cargo'));
        $empresa = trim((string) Arr::get($filtros, 'empresa'));
        $anios = Arr::get($filtros, 'anios_experiencia');

        if ($cargo !== '' || $empresa !== '') {
            $query->whereHas('experienciasLaborales', function (Builder $e) use ($cargo, $empresa) {
                $e->where('activo', true)
                    ->when($cargo !== '', fn (Builder $q) => $q->where('cargo', 'like', "%{$cargo}%"))
                    ->when($empresa !== '', fn (Builder $q) => $q->where('empresa', 'like', "%{$empresa}%"));
            });
        }

        if ($anios !== null && $anios !== '') {
            $query->whereRaw(
                "(SELECT COALESCE(SUM(TIMESTAMPDIFF(MONTH, desde, COALESCE(hasta, CURDATE()))), 0)
                  FROM tbl_consultor_experiencia_laboral exp
                  WHERE exp.id_consultor = tbl_consultor.id_consultor
                    AND exp.activo = 1
                    AND exp.deleted_at IS NULL) >= ?",
                [(int) $anios * 12]
            );
        }
    }
}