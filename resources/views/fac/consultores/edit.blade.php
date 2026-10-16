@extends('layouts.app')

@section(
    'title',
    $esMiPerfil
        ? 'Editar mi perfil | Facilitadores FEPADE'
        : 'Editar perfil | Facilitadores FEPADE'
)

@section(
    'page-title',
    $esMiPerfil
        ? 'Editar mi perfil'
        : 'Editar perfil'
)

@section(
    'page-subtitle',
    $esMiPerfil
        ? 'Actualiza tu información personal y profesional.'
        : 'Actualiza la información personal del consultor.'
)

@section('content')

{{-- Mejoras visuales compartidas: se mantienen campos, rutas y reglas de negocio. --}}
@include('fac.consultores.partials._fac_input_ux')
@include('fac.consultores.partials._perfil_select_file_ux')

@include(
    'fac.consultores.partials._wizard',
    [
        'step' => 1,
        'consultor' => $consultor,
    ]
)

<div class="perfil-panel mb-4">
    <form
        data-fac-ux-form
        method="POST"
        action="{{ route('fac.consultores.update', $consultor) }}"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        @include(
            'fac.consultores.partials._form',
            [
                'consultor' => $consultor,
                'catalogos' => $catalogos,
                'esMiPerfil' => $esMiPerfil,
                'puedeGestionarConsultores' => $puedeGestionarConsultores,
            ]
        )

        <hr class="my-4">

        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">

            <div class="d-flex align-items-center gap-2 flex-wrap">

                <a
                    href="{{ $esMiPerfil
                        ? route('fac.mi-perfil')
                        : route('fac.consultores.show', $consultor) }}"
                    class="btn btn-outline-secondary"
                >
                    Cancelar
                </a>

                <a
                    href="{{ $esMiPerfil
                        ? route('fac.mi-perfil')
                        : route('fac.consultores.show', $consultor) }}"
                    class="btn btn-outline-secondary"
                >
                    <i class="fa-solid fa-arrow-left me-1"></i>

                    {{ $esMiPerfil
                        ? 'Volver a mi perfil'
                        : 'Volver al expediente' }}
                </a>

            </div>

            <button type="submit" class="btn btn-fepade">
                <i class="fa-solid fa-floppy-disk me-1"></i>
                Guardar y continuar a Contacto
                <i class="fa-solid fa-arrow-right ms-1"></i>
            </button>

        </div>
    </form>
</div>

@endsection
