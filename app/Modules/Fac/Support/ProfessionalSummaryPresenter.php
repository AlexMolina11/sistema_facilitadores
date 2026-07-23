<?php

namespace App\Modules\Fac\Support;

use App\Modules\Fac\Models\Consultor;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProfessionalSummaryPresenter
{
    public static function make(Consultor $consultor): string
    {
        $nombre = trim($consultor->nombre_completo ?? (($consultor->nombres ?? '') . ' ' . ($consultor->apellidos ?? '')));
        $sujeto = filled($nombre) ? $nombre : 'Este consultor';

        $experiencias = self::collection($consultor->experienciasLaborales ?? null);
        $atestados = self::collection($consultor->atestados ?? null);
        $capacitacionesFepade = self::collection($consultor->capacitacionesFepade ?? null);
        $areasPerfil = self::collection($consultor->areasEspecializacion ?? null);
        $idiomas = self::collection($consultor->idiomas ?? null);
        $disponibilidades = self::collection($consultor->disponibilidades ?? null);
        $referencias = self::collection($consultor->referencias ?? null);

        $partes = [];

        $intro = $sujeto . ' cuenta con un expediente profesional registrado en el sistema de Facilitadores FEPADE';

        if ($experiencias->count() > 0) {
            $intro .= ' y experiencia laboral documentada en ' . self::plural($experiencias->count(), 'registro profesional', 'registros profesionales');
        }

        $partes[] = $intro . '.';

        $areas = self::areaNames($areasPerfil);
        if ($areas->count() > 0) {
            $partes[] = 'Su perfil muestra especialización en ' . self::joinHuman($areas->take(4)) . '.';
        }

        $habilidades = self::skillNames($areasPerfil);
        if ($habilidades->count() > 0) {
            $partes[] = 'Entre sus principales habilidades técnicas se encuentran ' . self::joinHuman($habilidades->take(6)) . '.';
        }

        $formacionTotal = $atestados->count() + $capacitacionesFepade->count();
        if ($formacionTotal > 0) {
            $partes[] = 'Registra trayectoria formativa respaldada por ' . self::plural($formacionTotal, 'evidencia académica o capacitación', 'evidencias académicas o capacitaciones') . '.';
        }

        $idiomaNames = self::languageNames($idiomas);
        if ($idiomaNames->count() > 0) {
            $partes[] = 'Además, registra dominio de ' . self::joinHuman($idiomaNames->take(4)) . '.';
        }

        if ($disponibilidades->count() > 0) {
            $partes[] = 'Cuenta con disponibilidad registrada para participar en procesos de consultoría o facilitación.';
        }

        if ($referencias->count() > 0) {
            $partes[] = 'El expediente incluye ' . self::plural($referencias->count(), 'referencia registrada', 'referencias registradas') . ' para respaldo del perfil.';
        }

        return implode(' ', $partes);
    }

    protected static function collection($value): Collection
    {
        if ($value instanceof Collection) {
            return $value;
        }

        if (is_iterable($value)) {
            return collect($value);
        }

        return collect();
    }

    protected static function areaNames(Collection $areasPerfil): Collection
    {
        return $areasPerfil
            ->map(function ($registroArea) {
                return $registroArea->areaEspecializacion->nombre
                    ?? $registroArea->area?->nombre
                    ?? $registroArea->nombre
                    ?? null;
            })
            ->filter()
            ->map(fn ($nombre) => trim((string) $nombre))
            ->filter()
            ->unique()
            ->values();
    }

    protected static function skillNames(Collection $areasPerfil): Collection
    {
        return $areasPerfil
            ->flatMap(function ($registroArea) {
                $habilidades = $registroArea->habilidades
                    ?? $registroArea->habilidadesTecnicas
                    ?? collect();

                return self::collection($habilidades)->map(function ($habilidad) {
                    return $habilidad->habilidadTecnica->nombre
                        ?? $habilidad->habilidad->nombre
                        ?? $habilidad->nombre
                        ?? null;
                });
            })
            ->filter()
            ->map(fn ($nombre) => trim((string) $nombre))
            ->filter()
            ->unique()
            ->values();
    }

    protected static function languageNames(Collection $idiomas): Collection
    {
        return $idiomas
            ->map(function ($registroIdioma) {
                return $registroIdioma->idioma->nombre
                    ?? $registroIdioma->idiomaCatalogo->nombre
                    ?? $registroIdioma->nombre
                    ?? null;
            })
            ->filter()
            ->map(fn ($nombre) => trim((string) $nombre))
            ->filter()
            ->unique()
            ->values();
    }

    protected static function joinHuman(Collection $items): string
    {
        $items = $items
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values();

        if ($items->count() === 0) {
            return '';
        }

        if ($items->count() === 1) {
            return $items->first();
        }

        $last = (string) $items->pop();
        $conjunction = self::conjunctionBefore($last);

        return $items->implode(', ') . ' ' . $conjunction . ' ' . $last;
    }

    protected static function conjunctionBefore(string $word): string
    {
        $word = Str::lower(trim($word));

        if ($word === '') {
            return 'y';
        }

        /*
        * Se mantiene "y" antes de palabras que comienzan con los
        * diptongos hie- o hia-, porque no tienen sonido inicial de "i".
        *
        * Ejemplos:
        * - agua y hielo
        * - leones y hienas
        */
        if (Str::startsWith($word, ['hie', 'hia'])) {
            return 'y';
        }

        /*
        * Se utiliza "e" antes de palabras que comienzan con:
        * - i
        * - í
        * - hi
        * - hí
        *
        * Ejemplos:
        * - Español e Inglés
        * - Francés e Italiano
        * - investigación e historia
        */
        if (Str::startsWith($word, ['i', 'í', 'hi', 'hí'])) {
            return 'e';
        }

        return 'y';
    }

    protected static function plural(int $count, string $singular, string $plural): string
    {
        return $count . ' ' . Str::of($count === 1 ? $singular : $plural);
    }
}
