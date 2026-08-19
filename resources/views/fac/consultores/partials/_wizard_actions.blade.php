@php
    /*
    |--------------------------------------------------------------------------
    | Contexto
    |--------------------------------------------------------------------------
    */

    $usuarioActual = auth()->user();

    $esMiPerfil =
        filled($usuarioActual?->id_consultor)
        && (int) $usuarioActual->id_consultor
            === (int) $consultor->id_consultor;

    /*
    |--------------------------------------------------------------------------
    | Configuración recibida
    |--------------------------------------------------------------------------
    */

    $anterior = $anterior ?? null;

    $continuarRoute =
        $continuarRoute ?? null;

    $continuarLabel =
        $continuarLabel ?? 'Continuar';

    $esUltimoPaso =
        $esUltimoPaso ?? false;

    /*
    |--------------------------------------------------------------------------
    | URL de cancelación
    |--------------------------------------------------------------------------
    */

    $urlSalir = $esMiPerfil
        ? route('fac.mi-perfil')
        : route(
            'fac.consultores.show',
            $consultor
        );
@endphp


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

    {{-- IZQUIERDA --}}
    <div
        class="
            d-flex
            align-items-center
            gap-2
            flex-wrap
        "
    >

        @if($anterior)

            <a
                href="{{ $anterior }}"
                class="btn btn-outline-secondary"
            >
                <i class="fa-solid fa-arrow-left me-1"></i>
                Anterior
            </a>

        @endif


        <a
            href="{{ $urlSalir }}"
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


    {{-- DERECHA --}}
    @if($continuarRoute)

        <form
            method="POST"
            action="{{ $continuarRoute }}"
            class="m-0"
        >

            @csrf


            <button
                type="submit"
                class="btn btn-fepade"
            >

                @if($esUltimoPaso)

                    <i class="fa-solid fa-circle-check me-1"></i>

                    {{
                        $esMiPerfil
                            ? 'Finalizar y ver mi perfil'
                            : 'Finalizar y ver expediente'
                    }}

                @else

                    {{ $continuarLabel }}

                    <i class="fa-solid fa-arrow-right ms-1"></i>

                @endif

            </button>

        </form>

    @endif

</div>