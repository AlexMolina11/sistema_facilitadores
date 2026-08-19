@extends('layouts.app')

@section('title', 'Detalle de invitación | Facilitadores FEPADE')
@section('page-title', 'Invitación')
@section('page-subtitle', 'Detalle y gestión del acceso del consultor')

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

    $claseEstado = match($estadoInvitacion) {
        'Activa' => 'success',
        'Vencida' => 'warning',
        'Revocada' => 'danger',
        'Consumida' => 'primary',
        default => 'muted',
    };
@endphp


<div class="invitacion-page">

    {{-- HERO --}}
    <section class="invitacion-hero">

        <div class="invitacion-hero-main">

            <div class="invitacion-hero-icon">
                <i class="fa-solid fa-envelope-open-text"></i>
            </div>

            <div>

                <div class="invitacion-kicker">
                    Invitación #{{ $invitacion->id_invitacion }}
                </div>

                <h2>
                    {{ $invitacion->consultor?->nombre_completo ?? $invitacion->alias }}
                </h2>

                <div class="invitacion-hero-meta">

                    @if($correoPrincipal)
                        <span>
                            <i class="fa-regular fa-envelope me-1"></i>
                            {{ $correoPrincipal }}
                        </span>
                    @endif

                    <span>
                        <i class="fa-solid fa-user-shield me-1"></i>
                        {{ $invitacion->rol?->nombre ?? 'Consultor' }}
                    </span>

                </div>

            </div>

        </div>


        <div class="invitacion-hero-actions">

            <span class="invitacion-status {{ $claseEstado }}">
                <i class="fa-solid fa-circle"></i>
                {{ $estadoInvitacion }}
            </span>

            <a
                href="{{ route('seg.invitaciones.index') }}"
                class="btn btn-light"
            >
                <i class="fa-solid fa-arrow-left me-1"></i>
                Regresar al listado
            </a>

        </div>

    </section>


    {{-- MÉTRICAS --}}
    <section class="invitacion-summary-grid">

        <div class="invitacion-stat-card">
            <span>Duración</span>

            <strong>
                @if($invitacion->duracion_horas === null)
                    Ilimitada
                @else
                    {{ $invitacion->duracion_horas }} h
                @endif
            </strong>
        </div>


        <div class="invitacion-stat-card">
            <span>Usos</span>

            <strong>
                @if($invitacion->max_usos === null)
                    {{ $invitacion->usos_actuales }} / ∞
                @else
                    {{ $invitacion->usos_actuales }}
                    /
                    {{ $invitacion->max_usos }}
                @endif
            </strong>
        </div>


        <div class="invitacion-stat-card">
            <span>Creada</span>

            <strong>
                {{ $invitacion->created_at?->format('d/m/Y') }}
            </strong>

            <small>
                {{ $invitacion->created_at?->format('h:i A') }}
            </small>
        </div>


        <div class="invitacion-stat-card">
            <span>Expiración</span>

            <strong>
                @if($invitacion->fecha_expiracion)
                    {{ $invitacion->fecha_expiracion->format('d/m/Y') }}
                @else
                    Sin límite
                @endif
            </strong>

            @if($invitacion->fecha_expiracion)
                <small>
                    {{ $invitacion->fecha_expiracion->format('h:i A') }}
                </small>
            @endif
        </div>

    </section>


    <div class="invitacion-layout">

        {{-- COLUMNA PRINCIPAL --}}
        <main class="invitacion-main">

            {{-- ACCESO --}}
            <section class="invitacion-panel">

                <div class="invitacion-panel-header">

                    <div class="invitacion-section-heading">

                        <div class="invitacion-section-icon">
                            <i class="fa-solid fa-link"></i>
                        </div>

                        <div>
                            <h4>Acceso de invitación</h4>

                            <p>
                                Enlace único asociado al perfil del consultor.
                            </p>
                        </div>

                    </div>

                </div>


                <label
                    for="urlInvitacion"
                    class="form-label"
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
                        class="btn btn-outline-primary"
                        id="btnCopiarUrl"
                    >
                        <i class="fa-solid fa-copy me-1"></i>
                        Copiar
                    </button>

                </div>


                <div class="invitacion-actions mt-4">

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
                                class="btn btn-fepade"
                            >
                                <i class="fa-solid fa-paper-plane me-1"></i>
                                Enviar por correo
                            </button>

                        </form>

                    @endif


                    @if($invitacion->ruta_qr)

                        <a
                            href="{{ route('seg.invitaciones.qr.download', $invitacion) }}"
                            class="btn btn-outline-primary"
                        >
                            <i class="fa-solid fa-download me-1"></i>
                            Descargar QR
                        </a>

                    @endif


                    @if($estadoInvitacion === 'Activa')

                        <form
                            method="POST"
                            action="{{ route('seg.invitaciones.revoke', $invitacion) }}"
                            onsubmit="return confirm(
                                '¿Deseas revocar esta invitación? El enlace dejará de ser válido inmediatamente.'
                            );"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="btn btn-outline-danger"
                            >
                                <i class="fa-solid fa-ban me-1"></i>
                                Revocar
                            </button>

                        </form>

                    @endif

                </div>


                @if(!$correoPrincipal)

                    <div class="invitacion-notice warning mt-4">

                        <i class="fa-solid fa-triangle-exclamation"></i>

                        <div>
                            <strong>
                                Sin correo disponible
                            </strong>

                            <span>
                                Comparte esta invitación manualmente mediante
                                URL o código QR.
                            </span>
                        </div>

                    </div>

                @endif

            </section>


            {{-- INFORMACIÓN --}}
            <section class="invitacion-panel">

                <div class="invitacion-panel-header">

                    <div class="invitacion-section-heading">

                        <div class="invitacion-section-icon">
                            <i class="fa-solid fa-circle-info"></i>
                        </div>

                        <div>
                            <h4>Información de la invitación</h4>

                            <p>
                                Configuración y trazabilidad administrativa.
                            </p>
                        </div>

                    </div>

                </div>


                <div class="invitacion-info-grid">

                    <div class="invitacion-info-item">
                        <span>Consultor</span>

                        <strong>
                            {{ $invitacion->consultor?->nombre_completo ?? $invitacion->alias }}
                        </strong>
                    </div>


                    <div class="invitacion-info-item">
                        <span>Rol asignado</span>

                        <strong>
                            {{ $invitacion->rol?->nombre ?? 'Consultor' }}
                        </strong>
                    </div>


                    <div class="invitacion-info-item">
                        <span>Creada por</span>

                        <strong>
                            {{
                                trim(
                                    ($invitacion->creador?->nombres ?? '')
                                    . ' '
                                    . ($invitacion->creador?->apellidos ?? '')
                                ) ?: 'Sistema'
                            }}
                        </strong>
                    </div>


                    <div class="invitacion-info-item">
                        <span>Última modificación</span>

                        <strong>
                            {{ $invitacion->updated_at?->format('d/m/Y h:i A') }}
                        </strong>
                    </div>

                </div>

            </section>

        </main>


        {{-- SIDEBAR QR --}}
        <aside class="invitacion-sidebar">

            <section class="invitacion-panel invitacion-qr-panel">

                <div class="invitacion-qr-icon">
                    <i class="fa-solid fa-qrcode"></i>
                </div>

                <h4>
                    Código QR
                </h4>

                <p>
                    Escanea el código para abrir directamente
                    el enlace de invitación.
                </p>


                @if(
                    $invitacion->ruta_qr
                    && \Illuminate\Support\Facades\Storage::disk('public')
                        ->exists($invitacion->ruta_qr)
                )

                    <div class="invitacion-qr-box">

                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::url($invitacion->ruta_qr) }}"
                            alt="Código QR de la invitación"
                        >

                    </div>


                    <a
                        href="{{ route('seg.invitaciones.qr.download', $invitacion) }}"
                        class="btn btn-fepade w-100"
                    >
                        <i class="fa-solid fa-download me-1"></i>
                        Descargar código QR
                    </a>

                @else

                    <div class="invitacion-notice warning">
                        El código QR no se encuentra disponible.
                    </div>

                @endif

            </section>

        </aside>

    </div>

</div>


{{-- MODAL ENVÍO DESPUÉS DE CREACIÓN --}}
@if(
    session('preguntar_envio_correo')
    && $correoPrincipal
    && $estadoInvitacion === 'Activa'
)

<div
    class="modal fade"
    id="modalEnviarInvitacion"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content invitacion-modal">

            <div class="modal-header">

                <div>
                    <h5 class="modal-title">
                        ¿Enviar invitación?
                    </h5>

                    <small class="text-muted">
                        La invitación fue creada correctamente.
                    </small>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <div class="invitacion-modal-icon">
                    <i class="fa-solid fa-paper-plane"></i>
                </div>

                <p class="mb-2">
                    El consultor tiene registrado el correo:
                </p>

                <strong>
                    {{ $correoPrincipal }}
                </strong>

                <p class="text-muted mt-3 mb-0">
                    ¿Deseas enviar ahora el enlace y código QR
                    para que pueda crear sus credenciales?
                </p>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal"
                >
                    Enviar después
                </button>


                <form
                    method="POST"
                    action="{{ route('seg.invitaciones.email.send', $invitacion) }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="btn btn-fepade"
                    >
                        <i class="fa-solid fa-paper-plane me-1"></i>
                        Enviar ahora
                    </button>

                </form>

            </div>

        </div>

    </div>
</div>

@endif

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const button = document.getElementById('btnCopiarUrl');
    const input = document.getElementById('urlInvitacion');

    if (button && input) {

        button.addEventListener('click', async function () {

            const original = this.innerHTML;

            try {

                await navigator.clipboard.writeText(input.value);

                this.innerHTML =
                    '<i class="fa-solid fa-check me-1"></i>Copiada';

            } catch (error) {

                input.select();
                input.setSelectionRange(0, 99999);

                document.execCommand('copy');

                this.innerHTML =
                    '<i class="fa-solid fa-check me-1"></i>Copiada';
            }

            setTimeout(() => {
                this.innerHTML = original;
            }, 1500);

        });

    }


    const modalElement = document.getElementById(
        'modalEnviarInvitacion'
    );

    if (modalElement) {

        const modal = new bootstrap.Modal(
            modalElement
        );

        modal.show();

    }

});
</script>

@endpush