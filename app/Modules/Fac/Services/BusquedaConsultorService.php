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
                'habilidades' => fn ($q) => $q->where('activo', true),
                'idiomas' => fn ($q) => $q->where('activo', true),
                'experienciasLaborales' => fn ($q) => $q->where('activo', true),
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
        $q = trim((string) Arr::get($filtros, 'q'));

        if ($q === '') {
            return;
        }

        $query->where(function (Builder $sub) use ($q) {
            $sub->where('nombres', 'like', "%{$q}%")
                ->orWhere('apellidos', 'like', "%{$q}%")
                ->orWhere('numero_identificacion', 'like', "%{$q}%")
                ->orWhere('nit', 'like', "%{$q}%")
                ->orWhere('nrc', 'like', "%{$q}%")
                ->orWhereHas('emails', fn (Builder $email) => $email->where('email', 'like', "%{$q}%"));
        });
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
        foreach (['area_especializacion', 'habilidades_tecnicas', 'habilidades_blandas'] as $campo) {
            $habilidades = array_filter((array) Arr::get($filtros, $campo, []));

            if ($habilidades === []) {
                continue;
            }

            $query->whereHas('habilidades', function (Builder $h) use ($habilidades) {
                $h->where('activo', true)->whereIn('id_habilidad', $habilidades);
            });
        }
    }

    private function aplicarEducacion(Builder $query, array $filtros): void
    {
        $tipoFormacion = Arr::get($filtros, 'tipo_formacion');
        $nivelAcademico = Arr::get($filtros, 'nivel_academico');
        $tipoAtestado = Arr::get($filtros, 'tipo_atestado');

        if (!$tipoFormacion && !$nivelAcademico && !$tipoAtestado) {
            return;
        }

        $query->whereHas('formaciones', function (Builder $f) use ($tipoFormacion, $nivelAcademico, $tipoAtestado) {
            $f->where('activo', true)
                ->when($tipoFormacion, fn (Builder $q) => $q->where('id_tipo_formacion', $tipoFormacion))
                ->when($nivelAcademico, fn (Builder $q) => $q->where('id_nivel_academico', $nivelAcademico))
                ->when($tipoAtestado, fn (Builder $q) => $q->where('id_tipo_atestado', $tipoAtestado));
        });
    }

    private function aplicarIdiomas(Builder $query, array $filtros): void
    {
        $idioma = Arr::get($filtros, 'idioma');
        $nivel = Arr::get($filtros, 'nivel_idioma');

        if (!$idioma && !$nivel) {
            return;
        }

        $query->whereHas('idiomas', function (Builder $i) use ($idioma, $nivel) {
            $i->where('activo', true)
                ->when($idioma, fn (Builder $q) => $q->where('id_idioma', $idioma))
                ->when($nivel, fn (Builder $q) => $q->where('id_idioma_nivel', '>=', $nivel));
        });
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