@extends('layouts.app')

@section('title', 'Invitaciones | Facilitadores FEPADE')
@section('page-title', 'Invitaciones')
@section('page-subtitle', 'Gestión de accesos para consultores')

@section('content')

<div class="invitacion-page">

    {{-- HERO --}}
    <section class="invitacion-hero">

        <div class="invitacion-hero-main">

            <div class="invitacion-hero-icon">
                <i class="fa-solid fa-envelope-open-text"></i>
            </div>

            <div>

                <div class="invitacion-kicker">
                    Seguridad · Accesos
                </div>

                <h2>
                    Gestión de invitaciones
                </h2>

                <div class="invitacion-hero-meta">

                    <span>
                        Administra los enlaces de acceso generados
                        para los consultores.
                    </span>

                </div>

            </div>

        </div>


        <div class="invitacion-hero-actions">

            <a
                href="{{ route('fac.consultores.index') }}"
                class="btn btn-light"
            >
                <i class="fa-solid fa-users me-1"></i>
                Ir a consultores
            </a>

        </div>

    </section>


    {{-- FILTROS --}}
    <section class="invitacion-panel">

        <div class="invitacion-panel-header">

            <div class="invitacion-section-heading">

                <div class="invitacion-section-icon">
                    <i class="fa-solid fa-filter"></i>
                </div>

                <div>
                    <h4>Filtros</h4>
                    <p>
                        Busca por consultor y estado de la invitación.
                    </p>
                </div>

            </div>

        </div>


        <form
            method="GET"
            action="{{ route('seg.invitaciones.index') }}"
            class="row g-3 align-items-end"
        >

            <div class="col-lg-6">

                <label
                    for="buscar"
                    class="form-label"
                >
                    Buscar
                </label>

                <input
                    type="text"
                    name="buscar"
                    id="buscar"
                    class="form-control"
                    value="{{ $buscar }}"
                    placeholder="Nombre, apellido, identificación o correo"
                >

            </div>


            <div class="col-lg-3">

                <label
                    for="estado"
                    class="form-label"
                >
                    Estado
                </label>

                <select
                    name="estado"
                    id="estado"
                    class="form-select"
                >

                    <option value="">
                        Todos
                    </option>

                    <option
                        value="activa"
                        @selected($estado === 'activa')
                    >
                        Activas
                    </option>

                    <option
                        value="vencida"
                        @selected($estado === 'vencida')
                    >
                        Vencidas
                    </option>

                    <option
                        value="consumida"
                        @selected($estado === 'consumida')
                    >
                        Consumidas
                    </option>

                    <option
                        value="revocada"
                        @selected($estado === 'revocada')
                    >
                        Revocadas
                    </option>

                    <option
                        value="inactiva"
                        @selected($estado === 'inactiva')
                    >
                        Inactivas
                    </option>

                </select>

            </div>


            <div class="col-lg-3">

                <div class="fepade-filter-actions">

                    <button
                        type="submit"
                        class="btn btn-fepade flex-fill"
                    >
                        <i class="fa-solid fa-magnifying-glass me-1"></i>
                        Buscar
                    </button>

                    <a
                        href="{{ route('seg.invitaciones.index') }}"
                        class="btn btn-outline-secondary flex-fill"
                    >
                        Limpiar
                    </a>

                </div>

            </div>

        </form>

    </section>


    {{-- LISTADO --}}
    <section class="invitacion-panel">

        <div class="invitacion-panel-header">

            <div class="invitacion-section-heading">

                <div class="invitacion-section-icon">
                    <i class="fa-solid fa-list"></i>
                </div>

                <div>
                    <h4>Invitaciones registradas</h4>

                    <p>
                        {{ $invitaciones->total() }}
                        {{ $invitaciones->total() === 1 ? 'invitación encontrada' : 'invitaciones encontradas' }}
                    </p>
                </div>

            </div>

        </div>


        @if($invitaciones->count())

            <div class="fepade-table-wrapper">

                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>Consultor</th>
                            <th>Estado</th>
                            <th>Duración</th>
                            <th>Usos</th>
                            <th>Expiración</th>
                            <th>Creada por</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>


                    <tbody>

                        @foreach($invitaciones as $invitacion)

                            @php
                                $estadoInvitacion = $invitacion->estado();

                                $correoPrincipal = $invitacion
                                    ->consultor
                                    ?->emails
                                    ?->firstWhere('principal', true)
                                    ?->email
                                    ?? $invitacion
                                        ->consultor
                                        ?->emails
                                        ?->first()
                                        ?->email;
                            @endphp


                            <tr>

                                <td>

                                    <div class="invitacion-list-consultor">

                                        <strong>
                                            {{ $invitacion->consultor?->nombre_completo ?? $invitacion->alias }}
                                        </strong>

                                        @if($correoPrincipal)
                                            <small>
                                                {{ $correoPrincipal }}
                                            </small>
                                        @endif

                                    </div>

                                </td>


                                <td>

                                    @switch($estadoInvitacion)

                                        @case('Activa')
                                            <span class="badge badge-success-soft">
                                                Activa
                                            </span>
                                            @break

                                        @case('Vencida')
                                            <span class="badge badge-warning-soft">
                                                Vencida
                                            </span>
                                            @break

                                        @case('Revocada')
                                            <span class="badge badge-danger-soft">
                                                Revocada
                                            </span>
                                            @break

                                        @case('Consumida')
                                            <span class="badge badge-primary-soft">
                                                Consumida
                                            </span>
                                            @break

                                        @default
                                            <span class="badge badge-muted-soft">
                                                {{ $estadoInvitacion }}
                                            </span>

                                    @endswitch

                                </td>


                                <td>

                                    @if($invitacion->duracion_horas === null)
                                        Ilimitada
                                    @else
                                        {{ $invitacion->duracion_horas }} h
                                    @endif

                                </td>


                                <td>

                                    @if($invitacion->max_usos === null)

                                        {{ $invitacion->usos_actuales }} / ∞

                                    @else

                                        {{ $invitacion->usos_actuales }}
                                        /
                                        {{ $invitacion->max_usos }}

                                    @endif

                                </td>


                                <td>

                                    @if($invitacion->fecha_expiracion)

                                        <strong>
                                            {{ $invitacion->fecha_expiracion->format('d/m/Y') }}
                                        </strong>

                                        <small class="d-block text-muted">
                                            {{ $invitacion->fecha_expiracion->format('h:i A') }}
                                        </small>

                                    @else

                                        <span class="text-muted">
                                            Sin expiración
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    {{
                                        trim(
                                            ($invitacion->creador?->nombres ?? '')
                                            . ' '
                                            . ($invitacion->creador?->apellidos ?? '')
                                        ) ?: 'Sistema'
                                    }}

                                    <small class="d-block text-muted">
                                        {{ $invitacion->created_at?->format('d/m/Y h:i A') }}
                                    </small>

                                </td>


                                <td class="text-end">

                                    <div class="invitacion-list-actions">

                                        <a
                                            href="{{ route('seg.invitaciones.show', $invitacion) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Ver detalle"
                                        >
                                            <i class="fa-solid fa-eye"></i>
                                        </a>


                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary js-copy-invitacion"
                                            data-url="{{ $invitacion->url_invitacion }}"
                                            title="Copiar URL"
                                        >
                                            <i class="fa-solid fa-copy"></i>
                                        </button>


                                        @if($invitacion->ruta_qr)

                                            <a
                                                href="{{ route('seg.invitaciones.qr.download', $invitacion) }}"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Descargar QR"
                                            >
                                                <i class="fa-solid fa-qrcode"></i>
                                            </a>

                                        @endif


                                        @if(
                                            $estadoInvitacion === 'Activa'
                                            && $correoPrincipal
                                        )

                                            <form
                                                method="POST"
                                                action="{{ route('seg.invitaciones.email.send', $invitacion) }}"
                                                onsubmit="return confirm(
                                                    '¿Deseas enviar esta invitación a {{ $correoPrincipal }}?'
                                                );"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Enviar por correo"
                                                >
                                                    <i class="fa-solid fa-paper-plane"></i>
                                                </button>

                                            </form>

                                        @endif


                                        @if($estadoInvitacion === 'Activa')

                                            <form
                                                method="POST"
                                                action="{{ route('seg.invitaciones.revoke', $invitacion) }}"
                                                onsubmit="return confirm(
                                                    '¿Deseas revocar esta invitación?'
                                                );"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Revocar"
                                                >
                                                    <i class="fa-solid fa-ban"></i>
                                                </button>

                                            </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            <div class="pagination-wrapper">
                {{ $invitaciones->links() }}
            </div>

        @else

            <div class="busqueda-empty-state">

                <div class="busqueda-empty-icon">
                    <i class="fa-solid fa-envelope-open"></i>
                </div>

                <h5>
                    No hay invitaciones para mostrar
                </h5>

                <p>
                    Ajusta los filtros o genera una invitación
                    desde el listado de consultores.
                </p>

                <a
                    href="{{ route('fac.consultores.index') }}"
                    class="btn btn-fepade"
                >
                    <i class="fa-solid fa-users me-1"></i>
                    Ir a consultores
                </a>

            </div>

        @endif

    </section>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    document
        .querySelectorAll('.js-copy-invitacion')
        .forEach(function (button) {

            button.addEventListener('click', async function () {

                const url = this.dataset.url;

                if (!url) {
                    return;
                }

                const icon = this.querySelector('i');

                try {

                    await navigator.clipboard.writeText(url);

                    icon?.classList.remove('fa-copy');
                    icon?.classList.add('fa-check');

                    this.title = 'URL copiada';

                    setTimeout(() => {

                        icon?.classList.remove('fa-check');
                        icon?.classList.add('fa-copy');

                        this.title = 'Copiar URL';

                    }, 1500);

                } catch (error) {

                    window.prompt(
                        'Copia la URL de la invitación:',
                        url
                    );

                }

            });

        });

});
</script>

@endpush