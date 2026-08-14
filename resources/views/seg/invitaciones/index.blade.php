@extends('layouts.app')

@section('title', 'Invitaciones | Facilitadores FEPADE')
@section('page-title', 'Invitaciones')
@section('page-subtitle', 'Gestión de invitaciones de consultores')

@section('content')

<x-ui.page-header
    title="Gestión de invitaciones"
    subtitle="Consulta y administra los accesos generados para los consultores."
>
    <a
        href="{{ route('fac.consultores.index') }}"
        class="btn btn-outline-light"
    >
        <i class="fa-solid fa-users me-1"></i>
        Consultores
    </a>
</x-ui.page-header>


{{-- FILTROS --}}
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">

        <form
            method="GET"
            action="{{ route('seg.invitaciones.index') }}"
            class="row g-3 align-items-end"
        >

            <div class="col-md-6">
                <label
                    for="buscar"
                    class="form-label fw-semibold"
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


            <div class="col-md-3">
                <label
                    for="estado"
                    class="form-label fw-semibold"
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


            <div class="col-md-3">
                <div class="d-flex gap-2">

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

    </div>
</div>


{{-- LISTADO --}}
<div class="card shadow-sm border-0">
    <div class="card-body">

        @if($invitaciones->count())

            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead>
                        <tr>
                            <th>Consultor</th>
                            <th>Estado</th>
                            <th>Duración</th>
                            <th>Usos</th>
                            <th>Expiración</th>
                            <th>Creada por</th>
                            <th class="text-end">
                                Acciones
                            </th>
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

                                {{-- CONSULTOR --}}
                                <td>
                                    <div class="fw-semibold">
                                        {{ $invitacion->consultor?->nombre_completo ?? $invitacion->alias }}
                                    </div>

                                    @if($correoPrincipal)
                                        <small class="text-muted">
                                            {{ $correoPrincipal }}
                                        </small>
                                    @endif
                                </td>


                                {{-- ESTADO --}}
                                <td>

                                    @switch($estadoInvitacion)

                                        @case('Activa')
                                            <span class="badge text-bg-success">
                                                Activa
                                            </span>
                                            @break

                                        @case('Vencida')
                                            <span class="badge text-bg-warning">
                                                Vencida
                                            </span>
                                            @break

                                        @case('Consumida')
                                            <span class="badge text-bg-primary">
                                                Consumida
                                            </span>
                                            @break

                                        @case('Revocada')
                                            <span class="badge text-bg-danger">
                                                Revocada
                                            </span>
                                            @break

                                        @default
                                            <span class="badge text-bg-secondary">
                                                {{ $estadoInvitacion }}
                                            </span>

                                    @endswitch

                                </td>


                                {{-- DURACIÓN --}}
                                <td>
                                    @if($invitacion->duracion_horas === null)
                                        Ilimitada
                                    @else
                                        {{ $invitacion->duracion_horas }}
                                        h
                                    @endif
                                </td>


                                {{-- USOS --}}
                                <td>
                                    @if($invitacion->max_usos === null)

                                        {{ $invitacion->usos_actuales }}
                                        / Ilimitados

                                    @else

                                        {{ $invitacion->usos_actuales }}
                                        /
                                        {{ $invitacion->max_usos }}

                                    @endif
                                </td>


                                {{-- EXPIRACIÓN --}}
                                <td>
                                    @if($invitacion->fecha_expiracion)

                                        {{ $invitacion->fecha_expiracion->format('d/m/Y') }}

                                        <small class="d-block text-muted">
                                            {{ $invitacion->fecha_expiracion->format('h:i A') }}
                                        </small>

                                    @else

                                        <span class="text-muted">
                                            Sin expiración
                                        </span>

                                    @endif
                                </td>


                                {{-- CREADA POR --}}
                                <td>

                                    {{ $invitacion->creador?->nombre_completo ?? 'Sistema' }}

                                    <small class="d-block text-muted">
                                        {{ $invitacion->created_at?->format('d/m/Y h:i A') }}
                                    </small>

                                </td>


                                {{-- ACCIONES --}}
                                <td class="text-end">

                                    <div
                                        class="d-flex justify-content-end gap-1 flex-wrap"
                                    >

                                        <a
                                            href="{{ route('seg.invitaciones.show', $invitacion) }}"
                                            class="btn btn-sm btn-outline-dark"
                                            title="Ver detalle"
                                        >
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        @if($invitacion->ruta_qr)

                                            <a
                                                href="{{ route('seg.invitaciones.qr.download', $invitacion) }}"
                                                class="btn btn-sm btn-outline-success"
                                                title="Descargar QR"
                                            >
                                                <i class="fa-solid fa-qrcode"></i>
                                            </a>

                                        @endif


                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary js-copy-invitacion"
                                            data-url="{{ $invitacion->url_invitacion }}"
                                            title="Copiar URL"
                                        >
                                            <i class="fa-solid fa-copy"></i>
                                        </button>


                                        @if($estadoInvitacion === 'Activa')

                                            <form
                                                method="POST"
                                                action="{{ route('seg.invitaciones.revoke', $invitacion) }}"
                                                onsubmit="return confirm('¿Deseas revocar esta invitación? El enlace dejará de ser válido inmediatamente.');"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Revocar invitación"
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


            <div class="mt-3">
                {{ $invitaciones->links() }}
            </div>

        @else

            <div class="text-center py-5">

                <i
                    class="fa-solid fa-envelope-open-text fa-2x text-muted mb-3"
                ></i>

                <h5>
                    No hay invitaciones para mostrar
                </h5>

                <p class="text-muted mb-3">
                    Las invitaciones generadas para consultores aparecerán aquí.
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

    </div>
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

                try {

                    await navigator.clipboard.writeText(url);

                    const icon = this.querySelector('i');

                    if (icon) {
                        icon.classList.remove('fa-copy');
                        icon.classList.add('fa-check');
                    }

                    this.title = 'URL copiada';

                    setTimeout(() => {

                        if (icon) {
                            icon.classList.remove('fa-check');
                            icon.classList.add('fa-copy');
                        }

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