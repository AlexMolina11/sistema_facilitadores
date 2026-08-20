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
        ? 'Mi experiencia | Facilitadores FEPADE'
        : 'Experiencia laboral | Facilitadores FEPADE'
)

@section(
    'page-title',
    $esMiPerfil
        ? 'Mi experiencia laboral'
        : 'Experiencia laboral'
)

@section(
    'page-subtitle',
    $esMiPerfil
        ? 'Registra y mantén actualizada tu trayectoria profesional.'
        : 'Gestiona la trayectoria profesional del consultor.'
)

@section('content')

<!--<x-ui.page-header
    :title="$esMiPerfil
        ? 'Mi experiencia laboral'
        : 'Experiencia laboral'"
    :subtitle="$esMiPerfil
        ? 'Registra tus cargos, empresas, períodos laborales y evidencias.'
        : 'Registra cargos, empresas, períodos laborales y evidencias del consultor.'"
/>-->

@include('fac.consultores.partials._wizard', ['step' => 3, 'consultor' => $consultor])

<div class="perfil-panel mb-4">
    <div class="perfil-section-header">
        <div><h4>Experiencia laboral</h4><p class="text-muted mb-0">Registra cada experiencia de forma individual.</p></div>
        <button type="button" class="btn btn-fepade" onclick="mostrarFormularioPerfil('crear-experiencia')">+ Añadir experiencia</button>
    </div>

    <div id="crear-experiencia" class="perfil-form-wrapper d-none">
        @include('fac.consultores.partials._experiencia_laboral_form', ['consultor' => $consultor, 'experiencia' => null])
    </div>

    @forelse($consultor->experienciasLaborales as $experiencia)
        <article class="perfil-card-item">
            <div class="perfil-card-icon">💼</div>
            <div class="perfil-card-body">
                <div class="d-flex justify-content-between gap-3">
                    <div>
                        <div class="perfil-card-title">{{ $experiencia->cargo ?: 'Cargo no registrado' }}</div>
                        <div class="perfil-card-subtitle">{{ $experiencia->empresa ?: 'Empresa no registrada' }}</div>
                        <div class="perfil-card-meta">
                            {{ $experiencia->desde ? ucfirst($experiencia->desde->translatedFormat('F Y')) : 'Sin fecha' }}
                            –
                            {{ $experiencia->trabajo_actual ? 'Actualidad' : ($experiencia->hasta ? ucfirst($experiencia->hasta->translatedFormat('F Y')) : 'Sin fecha') }}
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-link text-secondary" onclick="mostrarFormularioPerfil('editar-experiencia-{{ $experiencia->id_experiencia }}')">✎</button>
                        <form method="POST" action="{{ route('fac.consultores.experiencia.laboral.destroy', [$consultor, $experiencia]) }}" onsubmit="return confirm('¿Deseas eliminar esta experiencia laboral?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-link text-danger">🗑</button></form>
                    </div>
                </div>
                @if($experiencia->descripcion)<hr><p class="mb-2"><em>“{{ $experiencia->descripcion }}”</em></p>@endif
                <div class="perfil-card-meta">
                    @if($experiencia->jefe_nombre) Supervisor: {{ $experiencia->jefe_nombre }}<br>@endif
                    @if($experiencia->jefe_email) {{ $experiencia->jefe_email }}<br>@endif
                    @if($experiencia->jefe_telefono) {{ $experiencia->jefe_telefono }}<br>@endif
                    @if($experiencia->url_evidencia)<a href="{{ Storage::url($experiencia->url_evidencia) }}" target="_blank" class="fw-semibold">Ver Evidencia</a>@endif
                </div>
                <div id="editar-experiencia-{{ $experiencia->id_experiencia }}" class="perfil-form-wrapper d-none mt-3">
                    @include('fac.consultores.partials._experiencia_laboral_form', ['consultor' => $consultor, 'experiencia' => $experiencia])
                </div>
            </div>
        </article>
    @empty
        <div class="text-muted border rounded p-4">No hay experiencias laborales registradas.</div>
    @endforelse
</div>

@include(
    'fac.consultores.partials._wizard_actions',
    [
        'consultor' =>
            $consultor,

        'anterior' =>
            route(
                'fac.consultores.contacto.edit',
                $consultor
            ),

        'continuarRoute' =>
            route(
                'fac.consultores.experiencia.continuar',
                $consultor
            ),

        'continuarLabel' =>
            'Continuar a Formación',
    ]
)
@endsection

@push('scripts')
<script>
function mostrarFormularioPerfil(id){document.querySelectorAll('.perfil-form-wrapper').forEach(el=>el.classList.add('d-none'));const t=document.getElementById(id);if(t)t.classList.remove('d-none');}
function cerrarFormulariosPerfil(){document.querySelectorAll('.perfil-form-wrapper').forEach(el=>el.classList.add('d-none'));}
</script>
@endpush
