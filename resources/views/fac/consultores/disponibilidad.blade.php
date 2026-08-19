@extends('layouts.app')

@php
    $esMiPerfil =
        filled(auth()->user()?->id_consultor)
        && (int) auth()->user()->id_consultor
            === (int) $consultor->id_consultor;
@endphp


@section(
    'title',
    $esMiPerfil
        ? 'Mi disponibilidad | Facilitadores FEPADE'
        : 'Disponibilidad | Facilitadores FEPADE'
)

@section(
    'page-title',
    $esMiPerfil
        ? 'Mi disponibilidad'
        : 'Editar disponibilidad'
)

@section(
    'page-subtitle',
    $esMiPerfil
        ? 'Indica tu disponibilidad actual para participar en actividades de FEPADE.'
        : 'Selecciona la disponibilidad actual del consultor.'
)


@section('content')


<x-ui.page-header
    :title="$esMiPerfil
        ? 'Mi disponibilidad'
        : 'Disponibilidad'"
    :subtitle="$esMiPerfil
        ? 'Selecciona la opción que mejor representa tu disponibilidad actual.'
        : 'Selecciona la opción que representa la disponibilidad actual del consultor.'"
/>


@include(
    'fac.consultores.partials._wizard',
    [
        'step' => 8,
        'consultor' => $consultor,
    ]
)


<form
    method="POST"
    action="{{ route(
        'fac.consultores.disponibilidad.continuar',
        $consultor
    ) }}"
>

    @csrf


    <div class="perfil-panel mb-4">

        <div class="habilidad-panel">

            <h4>
                Disponibilidad
            </h4>

            <p class="text-muted">
                {{
                    $esMiPerfil
                        ? 'Selecciona una única opción según tu disponibilidad actual.'
                        : 'Selecciona una única situación de disponibilidad para el consultor.'
                }}
            </p>


            <div class="habilidad-grid">

                @foreach(
                    $catalogos['tiposDisponibilidad']
                    as $tipo
                )

                    <div class="form-check">

                        <input
                            class="form-check-input"
                            type="radio"
                            name="id_tipo_disponibilidad"
                            value="{{ $tipo->id_tipo_disponibilidad }}"
                            id="disp_{{ $tipo->id_tipo_disponibilidad }}"
                            {{
                                $consultor
                                    ->disponibilidades
                                    ->contains(
                                        'id_tipo_disponibilidad',
                                        $tipo->id_tipo_disponibilidad
                                    )
                                        ? 'checked'
                                        : ''
                            }}
                            required
                        >

                        <label
                            class="form-check-label"
                            for="disp_{{ $tipo->id_tipo_disponibilidad }}"
                        >
                            {{ $tipo->nombre }}
                        </label>

                    </div>

                @endforeach

            </div>

        </div>

    </div>


    <div
        class="
            d-flex
            justify-content-between
            align-items-center
            gap-2
            flex-wrap
            mt-4
        "
    >

        <div
            class="
                d-flex
                align-items-center
                gap-2
                flex-wrap
            "
        >

            <a
                href="{{ route(
                    'fac.consultores.referencias.edit',
                    $consultor
                ) }}"
                class="btn btn-outline-secondary"
            >
                <i class="fa-solid fa-arrow-left me-1"></i>
                Anterior
            </a>


            <a
                href="{{
                    $esMiPerfil
                        ? route('fac.mi-perfil')
                        : route(
                            'fac.consultores.show',
                            $consultor
                        )
                }}"
                class="btn btn-outline-secondary"
            >
                <i class="fa-solid fa-xmark me-1"></i>

                {{
                    $esMiPerfil
                        ? 'Salir a mi perfil'
                        : 'Volver al expediente'
                }}
            </a>

        </div>


        <button
            type="submit"
            class="btn btn-fepade"
        >
            <i class="fa-solid fa-circle-check me-1"></i>

            {{
                $esMiPerfil
                    ? 'Finalizar y ver mi perfil'
                    : 'Finalizar y ver expediente'
            }}
        </button>

    </div>

</form>

@endsection