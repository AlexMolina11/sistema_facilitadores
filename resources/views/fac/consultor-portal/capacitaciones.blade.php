@extends('layouts.app')

@section('title', 'Mis capacitaciones | Facilitadores FEPADE')
@section('page-title', 'Mis capacitaciones')
@section(
    'page-subtitle',
    'Historial de capacitaciones brindadas en FEPADE'
)

@section('content')

<div class="consultor-portal">

    {{-- HERO --}}
    <section class="consultor-portal-hero">

        <div class="consultor-portal-hero-main">

            <div class="consultor-portal-hero-icon">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>

            <div>

                <div class="consultor-portal-kicker">
                    Mi expediente · FEPADE
                </div>

                <h2>
                    Mis capacitaciones
                </h2>

                <p>
                    Historial institucional de las capacitaciones
                    que has brindado en FEPADE.
                </p>

            </div>

        </div>

        <a
            href="{{ route('fac.mi-perfil') }}"
            class="btn btn-light"
        >
            <i class="fa-solid fa-user me-1"></i>
            Mi perfil
        </a>

    </section>


    {{-- AVISO --}}
    <section class="consultor-portal-notice">

        <div class="consultor-portal-notice-icon">
            <i class="fa-solid fa-circle-info"></i>
        </div>

        <div>
            <strong>
                Información institucional
            </strong>

            <p>
                Este historial proviene de los sistemas institucionales
                de FEPADE y está disponible únicamente para consulta.
                No puedes modificar ni eliminar estos registros.
            </p>
        </div>

    </section>


    {{-- LISTADO --}}
    <section class="consultor-portal-panel">

        <div class="consultor-portal-panel-header">

            <div>
                <h4>
                    Historial de capacitaciones
                </h4>

                <p>
                    {{ $consultor->capacitacionesFepade->count() }}
                    {{
                        $consultor->capacitacionesFepade->count() === 1
                            ? 'registro encontrado'
                            : 'registros encontrados'
                    }}
                </p>
            </div>

        </div>


        @if($consultor->capacitacionesFepade->isNotEmpty())

            <div class="fepade-table-wrapper">

                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>Capacitación</th>
                            <th>Institución</th>
                            <th>Fecha</th>
                            <th>Modalidad</th>
                            <th>Horas</th>
                            <th>Fuente</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach(
                            $consultor->capacitacionesFepade
                            as $capacitacion
                        )

                            <tr>

                                <td>

                                    <strong>
                                        {{ $capacitacion->nombre_evento }}
                                    </strong>

                                    @if($capacitacion->codigo_evento_externo)
                                        <small class="d-block text-muted">
                                            Código:
                                            {{ $capacitacion->codigo_evento_externo }}
                                        </small>
                                    @endif

                                    @if($capacitacion->tema)
                                        <small class="d-block text-muted mt-1">
                                            {{ $capacitacion->tema }}
                                        </small>
                                    @endif

                                </td>


                                <td>
                                    {{ $capacitacion->institucion ?: '—' }}
                                </td>


                                <td>

                                    @if($capacitacion->fecha_inicio)

                                        <strong>
                                            {{
                                                \Illuminate\Support\Carbon::parse(
                                                    $capacitacion->fecha_inicio
                                                )->format('d/m/Y')
                                            }}
                                        </strong>

                                        @if(
                                            $capacitacion->fecha_fin
                                            && $capacitacion->fecha_fin != $capacitacion->fecha_inicio
                                        )
                                            <small class="d-block text-muted">
                                                al
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


                                <td>
                                    {{ $capacitacion->modalidad ?: '—' }}
                                </td>


                                <td>

                                    @if($capacitacion->horas !== null)

                                        {{ $capacitacion->horas }} h

                                    @else

                                        —

                                    @endif

                                </td>


                                <td>

                                    @if($capacitacion->fuente)

                                        <span class="badge badge-primary-soft">
                                            {{ $capacitacion->fuente }}
                                        </span>

                                    @else

                                        <span class="badge badge-muted-soft">
                                            FEPADE
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="consultor-portal-empty">

                <div class="consultor-portal-empty-icon">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>

                <h5>
                    Aún no tienes capacitaciones registradas
                </h5>

                <p>
                    Cuando FEPADE registre capacitaciones vinculadas
                    a tu perfil, aparecerán automáticamente aquí.
                </p>

            </div>

        @endif

    </section>

</div>

@endsection