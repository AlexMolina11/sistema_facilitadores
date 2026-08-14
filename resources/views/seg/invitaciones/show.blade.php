@extends('layouts.app')

@section('title', 'Detalle de invitación | Facilitadores FEPADE')
@section('page-title', 'Invitación')
@section('page-subtitle', 'Detalle y gestión de invitación')

@section('content')

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


<x-ui.page-header
    title="Detalle de invitación"
    subtitle="Consulta el enlace, QR y configuración de acceso del consultor."
>
    <a
        href="{{ route('seg.invitaciones.index') }}"
        class="btn btn-outline-light"
    >
        <i class="fa-solid fa-arrow-left me-1"></i>
        Volver
    </a>
</x-ui.page-header>


<div class="row g-4">

    {{-- INFORMACIÓN --}}
    <div class="col-lg-8">

        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start mb-4">

                    <div>
                        <h4 class="mb-1">
                            {{ $invitacion->consultor?->nombre_completo ?? $invitacion->alias }}
                        </h4>

                        @if($correoPrincipal)
                            <div class="text-muted">
                                <i class="fa-regular fa-envelope me-1"></i>
                                {{ $correoPrincipal }}
                            </div>
                        @endif
                    </div>


                    <div>

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

                    </div>

                </div>


                <div class="row g-4">

                    <div class="col-md-6">

                        <div class="text-muted small mb-1">
                            Rol asignado
                        </div>

                        <div class="fw-semibold">
                            {{ $invitacion->rol?->nombre ?? 'Consultor' }}
                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="text-muted small mb-1">
                            Creada por
                        </div>

                        <div class="fw-semibold">
                            {{
                                trim(
                                    ($invitacion->creador?->nombres ?? '')
                                    . ' '
                                    . ($invitacion->creador?->apellidos ?? '')
                                ) ?: 'Sistema'
                            }}
                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="text-muted small mb-1">
                            Fecha de creación
                        </div>

                        <div class="fw-semibold">
                            {{ $invitacion->created_at?->format('d/m/Y h:i A') }}
                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="text-muted small mb-1">
                            Fecha de expiración
                        </div>

                        <div class="fw-semibold">

                            @if($invitacion->fecha_expiracion)
                                {{ $invitacion->fecha_expiracion->format('d/m/Y h:i A') }}
                            @else
                                Sin expiración
                            @endif

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="text-muted small mb-1">
                            Duración
                        </div>

                        <div class="fw-semibold">

                            @if($invitacion->duracion_horas === null)
                                Ilimitada
                            @else
                                {{ $invitacion->duracion_horas }} horas
                            @endif

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="text-muted small mb-1">
                            Usos
                        </div>

                        <div class="fw-semibold">

                            @if($invitacion->max_usos === null)

                                {{ $invitacion->usos_actuales }}
                                / Ilimitados

                            @else

                                {{ $invitacion->usos_actuales }}
                                /
                                {{ $invitacion->max_usos }}

                            @endif

                        </div>

                    </div>

                </div>


                <hr class="my-4">


                <label
                    for="urlInvitacion"
                    class="form-label fw-semibold"
                >
                    URL de invitación
                </label>


                <div class="input-group">

                    <input
                        type="text"
                        id="urlInvitacion"
                        class="form-control"
                        value="{{ $invitacion->url_invitacion }}"
                        readonly
                    >

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        id="btnCopiarUrl"
                    >
                        <i class="fa-solid fa-copy me-1"></i>
                        Copiar
                    </button>

                </div>


                @if($estadoInvitacion === 'Activa')

                    <div class="mt-4">

                        <form
                            method="POST"
                            action="{{ route('seg.invitaciones.revoke', $invitacion) }}"
                            onsubmit="return confirm('¿Deseas revocar esta invitación? El enlace dejará de ser válido inmediatamente.');"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="btn btn-outline-danger"
                            >
                                <i class="fa-solid fa-ban me-1"></i>
                                Revocar invitación
                            </button>

                        </form>

                    </div>

                @endif

            </div>
        </div>

    </div>


    {{-- QR --}}
    <div class="col-lg-4">

        <div class="card shadow-sm border-0">
            <div class="card-body p-4 text-center">

                <h5 class="mb-2">
                    Código QR
                </h5>

                <p class="text-muted small mb-4">
                    Escanea este código para abrir el enlace de invitación.
                </p>


                @if(
                    $invitacion->ruta_qr
                    && Storage::disk('public')->exists($invitacion->ruta_qr)
                )

                    <div class="mb-4">

                        <img
                            src="{{ Storage::url($invitacion->ruta_qr) }}"
                            alt="Código QR de la invitación"
                            class="img-fluid"
                            style="max-width: 280px;"
                        >

                    </div>


                    <a
                        href="{{ route('seg.invitaciones.qr.download', $invitacion) }}"
                        class="btn btn-fepade w-100"
                    >
                        <i class="fa-solid fa-download me-1"></i>
                        Descargar QR
                    </a>

                @else

                    <div class="alert alert-warning mb-0">
                        El código QR no está disponible.
                    </div>

                @endif

            </div>
        </div>

    </div>

</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const button = document.getElementById('btnCopiarUrl');
    const input = document.getElementById('urlInvitacion');

    if (!button || !input) {
        return;
    }

    button.addEventListener('click', async function () {

        const url = input.value;

        try {

            await navigator.clipboard.writeText(url);

            const original = this.innerHTML;

            this.innerHTML =
                '<i class="fa-solid fa-check me-1"></i>Copiada';

            setTimeout(() => {
                this.innerHTML = original;
            }, 1500);

        } catch (error) {

            input.select();
            input.setSelectionRange(0, 99999);

            document.execCommand('copy');

        }

    });

});
</script>
@endpush