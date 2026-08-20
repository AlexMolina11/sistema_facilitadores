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


<!--<x-ui.page-header
    :title="$esMiPerfil
        ? 'Mi información personal'
        : 'Perfil personal'"
    :subtitle="$esMiPerfil
        ? 'Mantén actualizados tus datos personales, residencia, documentos y fotografía.'
        : 'Actualiza los datos personales, residencia, documentos y fotografía del consultor.'"
>

    <div class="d-flex gap-2 flex-wrap">

        @if($esMiPerfil)

            <a
                href="{{ route('fac.mi-perfil') }}"
                class="btn btn-outline-secondary"
            >
                <i class="fa-solid fa-arrow-left me-1"></i>
                Volver a mi perfil
            </a>

        @else

            <a
                href="{{ route(
                    'fac.consultores.show',
                    $consultor
                ) }}"
                class="btn btn-outline-secondary"
            >
                <i class="fa-solid fa-arrow-left me-1"></i>
                Volver al expediente
            </a>

        @endif

    </div>

</x-ui.page-header>-->


<div
    class="
        d-flex
        justify-content-end
        mb-3
    "
>

    @if($esMiPerfil)

        <a
            href="{{ route('fac.mi-perfil') }}"
            class="btn btn-sm btn-outline-secondary"
        >
            <i class="fa-solid fa-arrow-left me-1"></i>
            Volver a mi perfil
        </a>

    @else

        <a
            href="{{ route(
                'fac.consultores.show',
                $consultor
            ) }}"
            class="btn btn-sm btn-outline-secondary"
        >
            <i class="fa-solid fa-arrow-left me-1"></i>
            Volver al expediente
        </a>

    @endif

</div>

@include(
    'fac.consultores.partials._wizard',
    [
        'step' => 1,
        'consultor' => $consultor,
    ]
)

<div class="perfil-panel mb-4">

    <form
        method="POST"
        action="{{ route(
            'fac.consultores.update',
            $consultor
        ) }}"
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
                'puedeGestionarConsultores' =>
                    $puedeGestionarConsultores,
            ]
        )


        <hr class="my-4">


        <div
            class="d-flex
                   justify-content-between
                   align-items-center
                   gap-2
                   flex-wrap"
        >

            @if($esMiPerfil)

                <a
                    href="{{ route('fac.mi-perfil') }}"
                    class="btn btn-outline-secondary"
                >
                    Cancelar
                </a>

            @else

                <a
                    href="{{ route(
                        'fac.consultores.show',
                        $consultor
                    ) }}"
                    class="btn btn-outline-secondary"
                >
                    Cancelar
                </a>

            @endif


            <button
                type="submit"
                class="btn btn-fepade"
            >
                <i class="fa-solid fa-floppy-disk me-1"></i>
                Guardar y continuar a Contacto
                <i class="fa-solid fa-arrow-right ms-1"></i>
            </button>

        </div>

    </form>

</div>

@endsection