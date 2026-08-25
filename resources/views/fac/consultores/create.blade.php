@extends('layouts.app')

@section('title', 'Nuevo consultor | Facilitadores FEPADE')
@section('page-title', 'Nuevo consultor')
@section(
    'page-subtitle',
    'Registro inicial del perfil del consultor'
)

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Contexto de creación
    |--------------------------------------------------------------------------
    |
    | En esta pantalla todavía no existe un consultor asociado al usuario
    | que estamos creando, por lo que nunca se considera "Mi perfil".
    |
    | El acceso a los controles administrativos se determina utilizando
    | el permiso real del usuario autenticado.
    |
    */

    $esMiPerfil = false;

    $puedeGestionarConsultores =
        auth()->user()?->tienePermiso(
            'fac.consultores.gestionar'
        ) ?? false;
@endphp


<section class="busqueda-hero mb-4">

    <div class="busqueda-hero-main">

        <span class="busqueda-hero-icon">
            <i class="fa-solid fa-plus"></i>
        </span>

        <div>

            <h2>
                Nuevo consultor
            </h2>

            <p>
                Completa la información base del consultor.
            </p>

        </div>

    </div>


    <div class="busqueda-hero-actions">

        <a
            href="{{ route('fac.consultores.index') }}"
            class="btn btn-outline-light"
        >
            <i class="fa-solid fa-xmark me-1"></i>
            Cancelar
        </a>

    </div>

</section>


<div class="fepade-card">

    <form
        method="POST"
        action="{{ route('fac.consultores.store') }}"
        enctype="multipart/form-data"
    >

        @csrf


        @include(
            'fac.consultores.partials._form',
            [
                'consultor' => null,
                'catalogos' => $catalogos,
                'esMiPerfil' => $esMiPerfil,
                'puedeGestionarConsultores' =>
                    $puedeGestionarConsultores,
            ]
        )


        <hr class="my-4">


        <div
            class="
                d-flex
                justify-content-end
                gap-2
                flex-wrap
            "
        >

            <a
                href="{{ route('fac.consultores.index') }}"
                class="btn btn-outline-secondary"
            >
                <i class="fa-solid fa-xmark me-1"></i>
                Cancelar
            </a>


            <button
                type="submit"
                class="btn btn-fepade"
            >
                <i class="fa-solid fa-floppy-disk me-1"></i>
                Guardar consultor
            </button>

        </div>

    </form>

</div>

@endsection