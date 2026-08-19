@extends('layouts.app')

@section('title', 'Mis capacitaciones | Facilitadores FEPADE')

@section(
    'page-title',
    'Mis capacitaciones'
)

@section(
    'page-subtitle',
    'Historial institucional de capacitaciones brindadas en FEPADE'
)


@section('content')

@php

    $capacitaciones =
        $consultor->capacitacionesFepade;

    $totalCapacitaciones =
        $capacitaciones->count();

    $totalHoras =
        $capacitaciones
            ->whereNotNull('no_horas_real')
            ->sum('no_horas_real');

    $capacitacionesEvaluadas =
        $capacitaciones
            ->whereNotNull('promedio_encuesta')
            ->count();

    $promedioGeneral =
        $capacitaciones
            ->whereNotNull('promedio_encuesta')
            ->avg('promedio_encuesta');

@endphp


{{-- =========================================================
    ENCABEZADO
========================================================= --}}

<x-ui.page-header
    title="Mis capacitaciones"
    subtitle="Consulta las capacitaciones que has brindado en FEPADE y los resultados de evaluación disponibles."
>

    <a
        href="{{ route('fac.mi-perfil') }}"
        class="btn btn-outline-secondary"
    >
        <i class="fa-solid fa-arrow-left me-1"></i>
        Volver a mi perfil
    </a>

</x-ui.page-header>


{{-- =========================================================
    RESUMEN
========================================================= --}}

<x-ui.dashboard-grid
    :columns="3"
    class="mb-4"
>

    <x-ui.stat-card
        label="Capacitaciones impartidas"
        :value="$totalCapacitaciones"
        description="Registros institucionales"
        icon="fa-chalkboard-user"
    />


    <x-ui.stat-card
        label="Horas impartidas"
        :value="$totalHoras"
        description="Horas reales registradas"
        icon="fa-clock"
        variant="success"
    />


    <x-ui.stat-card
        label="Promedio de evaluación"
        :value="!is_null($promedioGeneral)
            ? number_format($promedioGeneral, 2)
            : '—'"
        :description="$capacitacionesEvaluadas > 0
            ? $capacitacionesEvaluadas . ' capacitaciones evaluadas'
            : 'Aún no hay evaluaciones disponibles'"
        icon="fa-star"
        variant="warning"
    />

</x-ui.dashboard-grid>


{{-- =========================================================
    AVISO INSTITUCIONAL
========================================================= --}}

<x-ui.page-card class="mb-4">

    <div class="d-flex align-items-start gap-3">

        <div
            class="
                badge
                badge-primary-soft
                rounded-circle
                d-inline-flex
                align-items-center
                justify-content-center
                flex-shrink-0
            "
            style="
                width: 42px;
                height: 42px;
            "
        >

            <i class="fa-solid fa-circle-info"></i>

        </div>


        <div>

            <h5 class="mb-1">
                Información institucional
            </h5>

            <p class="text-muted mb-0">

                Este historial proviene de los sistemas
                institucionales de FEPADE y está disponible
                únicamente para consulta.

                Las capacitaciones y sus evaluaciones no pueden
                modificarse ni eliminarse desde este portal.

            </p>

        </div>

    </div>

</x-ui.page-card>


{{-- =========================================================
    HISTORIAL
========================================================= --}}

<x-ui.table-card

    title="Historial de capacitaciones"

    subtitle="Selecciona una capacitación para consultar su información completa y los resultados de evaluación."

    :items="$capacitaciones"

    emptyTitle="Aún no tienes capacitaciones registradas."

    emptyMessage="Cuando FEPADE registre capacitaciones vinculadas a tu perfil, aparecerán automáticamente aquí."

>

    <table class="table table-hover align-middle">

        <thead>

            <tr>

                <th>
                    Capacitación
                </th>

                <th>
                    Fecha
                </th>

                <th>
                    Modalidad y horas
                </th>

                <th>
                    Evaluación
                </th>

                <th class="text-end">
                    Acciones
                </th>

            </tr>

        </thead>


        <tbody>

            @foreach(
                $capacitaciones
                as $capacitacion
            )

                <tr>

                    {{-- =========================================
                        CAPACITACIÓN
                    ========================================== --}}

                    <td style="min-width: 280px;">

                        <strong class="d-block mb-1">

                            {{
                                $capacitacion->curso_nombre
                                ?: 'Capacitación FEPADE'
                            }}

                        </strong>


                        @if($capacitacion->cliente)

                            <small class="text-muted d-block">

                                <i class="fa-regular fa-building me-1"></i>

                                {{
                                    $capacitacion->cliente
                                }}

                            </small>

                        @endif


                        @if($capacitacion->codigo_evento)

                            <small class="text-muted d-block mt-1">

                                Código:
                                {{
                                    $capacitacion->codigo_evento
                                }}

                            </small>

                        @endif


                        @if($capacitacion->tipo_evento_nombre)

                            <small class="text-muted d-block">

                                {{
                                    $capacitacion
                                        ->tipo_evento_nombre
                                }}

                            </small>

                        @endif

                    </td>


                    {{-- =========================================
                        FECHA
                    ========================================== --}}

                    <td>

                        @if($capacitacion->fecha_inicio)

                            <strong class="d-block">

                                {{
                                    \Illuminate\Support\Carbon::parse(
                                        $capacitacion->fecha_inicio
                                    )->format('d/m/Y')
                                }}

                            </strong>


                            @if(
                                $capacitacion->fecha_fin
                                && $capacitacion->fecha_fin
                                    != $capacitacion->fecha_inicio
                            )

                                <small class="text-muted">

                                    hasta

                                    {{
                                        \Illuminate\Support\Carbon::parse(
                                            $capacitacion->fecha_fin
                                        )->format('d/m/Y')
                                    }}

                                </small>

                            @endif

                        @else

                            <span class="text-muted">
                                —
                            </span>

                        @endif

                    </td>


                    {{-- =========================================
                        MODALIDAD / HORAS
                    ========================================== --}}

                    <td>

                        <div class="d-flex flex-column gap-1">

                            @if($capacitacion->modalidad)

                                <div>

                                    <span class="badge badge-muted-soft">

                                        <i class="fa-solid fa-location-dot me-1"></i>

                                        {{
                                            $capacitacion->modalidad
                                        }}

                                    </span>

                                </div>

                            @endif


                            @if(
                                $capacitacion->no_horas_real
                                !== null
                            )

                                <small class="text-muted">

                                    <i class="fa-regular fa-clock me-1"></i>

                                    {{
                                        $capacitacion->no_horas_real
                                    }}

                                    horas

                                </small>

                            @endif

                        </div>

                    </td>


                    {{-- =========================================
                        EVALUACIÓN
                    ========================================== --}}

                    <td style="min-width: 180px;">

                        @if(
                            $capacitacion->promedio_encuesta
                            !== null
                        )

                            <div class="d-flex align-items-center gap-2">

                                <span
                                    class="
                                        badge
                                        badge-success-soft
                                        px-3
                                        py-2
                                    "
                                >

                                    <i class="fa-solid fa-star me-1"></i>

                                    {{
                                        number_format(
                                            $capacitacion
                                                ->promedio_encuesta,
                                            2
                                        )
                                    }}

                                </span>

                            </div>


                            @if(
                                $capacitacion
                                    ->encuesta_nombre
                            )

                                <small
                                    class="
                                        text-muted
                                        d-block
                                        mt-1
                                    "
                                >

                                    {{
                                        $capacitacion
                                            ->encuesta_nombre
                                    }}

                                </small>

                            @endif

                        @else

                            <span class="badge badge-warning-soft">

                                <i class="fa-regular fa-clock me-1"></i>
                                Evaluación pendiente

                            </span>

                        @endif

                    </td>


                    {{-- =========================================
                        ACCIONES
                    ========================================== --}}

                    <td class="text-end">

                        <x-ui.action-buttons

                            :show-url="route(
                                'fac.mis-capacitaciones.show',
                                $capacitacion
                                    ->id_capacitacion_fepade
                            )"

                        />

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</x-ui.table-card>


@endsection