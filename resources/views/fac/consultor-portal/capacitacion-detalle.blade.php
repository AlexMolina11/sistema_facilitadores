@extends('layouts.app')

@section(
    'title',
    'Detalle de capacitación | Facilitadores FEPADE'
)

@section(
    'page-title',
    'Detalle de capacitación'
)

@section(
    'page-subtitle',
    'Información institucional y resultados de evaluación'
)


@section('content')

@php

    $tieneEvaluacion =
        $capacitacion->promedio_encuesta
        !== null;

    $estadoCurso =
        trim(
            (string) $capacitacion
                ->estado_curso_nombre
        );

    $estadoClass = match(true) {

        str_contains(
            mb_strtolower($estadoCurso),
            'final'
        ) =>
            'badge-success-soft',

        str_contains(
            mb_strtolower($estadoCurso),
            'cancel'
        ) =>
            'badge-danger-soft',

        default =>
            'badge-primary-soft',
    };

@endphp


{{-- =========================================================
    HEADER
========================================================= --}}

<x-ui.page-header

    title="Detalle de capacitación"

    subtitle="Consulta la información registrada para esta capacitación y sus resultados de evaluación."

>

    <a
        href="{{ route('fac.mis-capacitaciones') }}"
        class="btn btn-outline-secondary"
    >
        <i class="fa-solid fa-arrow-left me-1"></i>
        Volver a mis capacitaciones
    </a>

</x-ui.page-header>


{{-- =========================================================
    RESUMEN PRINCIPAL
========================================================= --}}

<x-ui.page-card class="mb-4">

    <div class="row g-4 align-items-center">

        <div class="col-lg-8">

            <div
                class="
                    text-muted
                    text-uppercase
                    small
                    fw-semibold
                    mb-2
                "
            >
                Capacitación FEPADE
            </div>


            <h3 class="mb-2">

                {{
                    $capacitacion->curso_nombre
                    ?: 'Capacitación FEPADE'
                }}

            </h3>


            <div
                class="
                    d-flex
                    flex-wrap
                    align-items-center
                    gap-2
                    mb-3
                "
            >

                @if($capacitacion->cliente)

                    <span class="badge badge-muted-soft">

                        <i class="fa-regular fa-building me-1"></i>

                        {{
                            $capacitacion->cliente
                        }}

                    </span>

                @endif


                @if($capacitacion->modalidad)

                    <span class="badge badge-muted-soft">

                        <i class="fa-solid fa-location-dot me-1"></i>

                        {{
                            $capacitacion->modalidad
                        }}

                    </span>

                @endif


                @if($estadoCurso)

                    <span class="badge {{ $estadoClass }}">

                        {{
                            $estadoCurso
                        }}

                    </span>

                @endif

            </div>


            <div
                class="
                    d-flex
                    flex-wrap
                    gap-4
                    text-muted
                    small
                "
            >

                <span>

                    <i class="fa-regular fa-calendar me-1"></i>

                    @if($capacitacion->fecha_inicio)

                        {{
                            \Illuminate\Support\Carbon::parse(
                                $capacitacion->fecha_inicio
                            )->format('d/m/Y')
                        }}

                    @else

                        Sin fecha

                    @endif


                    @if(
                        $capacitacion->fecha_fin
                        && $capacitacion->fecha_fin
                            != $capacitacion->fecha_inicio
                    )

                        —

                        {{
                            \Illuminate\Support\Carbon::parse(
                                $capacitacion->fecha_fin
                            )->format('d/m/Y')
                        }}

                    @endif

                </span>


                <span>

                    <i class="fa-regular fa-clock me-1"></i>

                    @if(
                        $capacitacion->no_horas_real
                        !== null
                    )

                        {{
                            $capacitacion
                                ->no_horas_real
                        }}

                        horas

                    @else

                        Horas no registradas

                    @endif

                </span>

            </div>

        </div>


        <div class="col-lg-4">

            <div
                class="
                    text-lg-end
                    text-start
                "
            >

                <div class="text-muted small mb-1">
                    Código del evento
                </div>

                <strong>

                    {{
                        $capacitacion->codigo_evento
                        ?: 'No registrado'
                    }}

                </strong>

            </div>

        </div>

    </div>

</x-ui.page-card>


{{-- =========================================================
    CONTENIDO PRINCIPAL
========================================================= --}}

<div class="row g-4">

    {{-- =====================================================
        EVALUACIÓN
    ====================================================== --}}

    <div class="col-xl-5">

        <x-ui.page-card
            title="Evaluación de la capacitación"
            subtitle="Resultado registrado para esta actividad."
            class="h-100"
        >

            @if($tieneEvaluacion)

                <div class="text-center py-4">

                    <div
                        class="
                            badge
                            badge-success-soft
                            rounded-circle
                            d-inline-flex
                            align-items-center
                            justify-content-center
                            mb-3
                        "
                        style="
                            width: 70px;
                            height: 70px;
                            font-size: 1.25rem;
                        "
                    >

                        <i class="fa-solid fa-star"></i>

                    </div>


                    <div class="text-muted small mb-1">
                        Promedio obtenido
                    </div>


                    <div
                        class="
                            display-4
                            fw-bold
                            mb-2
                        "
                    >

                        {{
                            number_format(
                                $capacitacion
                                    ->promedio_encuesta,
                                2
                            )
                        }}

                    </div>


                    <p class="text-muted small mb-4">

                        Resultado de la evaluación
                        registrada para esta capacitación.

                    </p>


                    <hr>


                    <div class="row g-3 text-start">

                        <div class="col-12">

                            <div class="text-muted small">
                                Encuesta
                            </div>

                            <strong>

                                {{
                                    $capacitacion
                                        ->encuesta_nombre
                                    ?: 'No especificada'
                                }}

                            </strong>

                        </div>


                        <div class="col-12">

                            <div class="text-muted small">
                                Fecha de evaluación
                            </div>

                            <strong>

                                @if(
                                    $capacitacion
                                        ->fecha_evaluacion
                                )

                                    {{
                                        \Illuminate\Support\Carbon::parse(
                                            $capacitacion
                                                ->fecha_evaluacion
                                        )->format(
                                            'd/m/Y h:i A'
                                        )
                                    }}

                                @else

                                    No registrada

                                @endif

                            </strong>

                        </div>

                    </div>

                </div>

            @else

                <x-ui.empty-state

                    icon="fa-regular fa-star"

                    title="Evaluación pendiente"

                    message="Los resultados de esta capacitación todavía no han sido registrados por FEPADE. Cuando estén disponibles aparecerán automáticamente en este espacio."

                />

            @endif

        </x-ui.page-card>

    </div>


    {{-- =====================================================
        DATOS DEL EVENTO
    ====================================================== --}}

    <div class="col-xl-7">

        <x-ui.page-card

            title="Información del evento"

            subtitle="Datos institucionales asociados a esta capacitación."

            class="h-100"

        >

            <div class="row g-4">

                <div class="col-md-6">

                    <div class="text-muted small">
                        Curso
                    </div>

                    <strong>

                        {{
                            $capacitacion->curso_nombre
                            ?: 'No registrado'
                        }}

                    </strong>

                </div>


                <div class="col-md-6">

                    <div class="text-muted small">
                        Cliente
                    </div>

                    <strong>

                        {{
                            $capacitacion->cliente
                            ?: 'No registrado'
                        }}

                    </strong>

                </div>


                <div class="col-md-6">

                    <div class="text-muted small">
                        Tipo de evento
                    </div>

                    <strong>

                        {{
                            $capacitacion
                                ->tipo_evento_nombre
                            ?: 'No registrado'
                        }}

                    </strong>

                </div>


                <div class="col-md-6">

                    <div class="text-muted small">
                        Modalidad
                    </div>

                    <strong>

                        {{
                            $capacitacion->modalidad
                            ?: 'No registrada'
                        }}

                    </strong>

                </div>


                <div class="col-md-6">

                    <div class="text-muted small">
                        Fecha de inicio
                    </div>

                    <strong>

                        @if($capacitacion->fecha_inicio)

                            {{
                                \Illuminate\Support\Carbon::parse(
                                    $capacitacion
                                        ->fecha_inicio
                                )->format('d/m/Y')
                            }}

                        @else

                            —

                        @endif

                    </strong>

                </div>


                <div class="col-md-6">

                    <div class="text-muted small">
                        Fecha de finalización
                    </div>

                    <strong>

                        @if($capacitacion->fecha_fin)

                            {{
                                \Illuminate\Support\Carbon::parse(
                                    $capacitacion
                                        ->fecha_fin
                                )->format('d/m/Y')
                            }}

                        @else

                            —

                        @endif

                    </strong>

                </div>


                <div class="col-md-6">

                    <div class="text-muted small">
                        Horas reales
                    </div>

                    <strong>

                        @if(
                            $capacitacion
                                ->no_horas_real
                            !== null
                        )

                            {{
                                $capacitacion
                                    ->no_horas_real
                            }}

                            horas

                        @else

                            No registradas

                        @endif

                    </strong>

                </div>


                <div class="col-md-6">

                    <div class="text-muted small">
                        Estado
                    </div>

                    @if($estadoCurso)

                        <span class="badge {{ $estadoClass }}">

                            {{
                                $estadoCurso
                            }}

                        </span>

                    @else

                        <span class="text-muted">
                            No registrado
                        </span>

                    @endif

                </div>

            </div>

        </x-ui.page-card>

    </div>

</div>


{{-- =========================================================
    REFERENCIA INSTITUCIONAL
========================================================= --}}

<x-ui.page-card

    title="Referencia institucional"

    subtitle="Información técnica de origen del registro."

    class="mt-4"

>

    <div class="row g-4">

        <div class="col-md-4">

            <div class="text-muted small">
                Fuente
            </div>

            <span class="badge badge-primary-soft">

                {{
                    $capacitacion->fuente
                    ?: 'FEPADE'
                }}

            </span>

        </div>


        <div class="col-md-4">

            <div class="text-muted small">
                Programa / curso
            </div>

            <strong>

                {{
                    $capacitacion
                        ->programa_curso_id
                    ?: '—'
                }}

            </strong>

        </div>


        <div class="col-md-4">

            <div class="text-muted small">
                Última actualización institucional
            </div>

            <strong>

                @if(
                    $capacitacion
                        ->fecha_ultima_sincronizacion_saf
                )

                    {{
                        \Illuminate\Support\Carbon::parse(
                            $capacitacion
                                ->fecha_ultima_sincronizacion_saf
                        )->format(
                            'd/m/Y h:i A'
                        )
                    }}

                @else

                    —

                @endif

            </strong>

        </div>

    </div>

</x-ui.page-card>


@endsection