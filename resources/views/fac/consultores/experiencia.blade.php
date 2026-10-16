@extends('layouts.app')

@php
    $esMiPerfil = filled(auth()->user()?->id_consultor)
        && (int) auth()->user()->id_consultor === (int) $consultor->id_consultor;
    $experiencias = $consultor->experienciasLaborales->sortByDesc(function ($item) {
        return $item->desde?->format('Y-m-d') ?? '0000-00-00';
    })->values();
    $totalExperiencias = $experiencias->count();
    $trabajosActuales = $experiencias->filter(fn ($item) => (bool) $item->trabajo_actual)->count();
    $experienciasComparacion = $experiencias->map(fn ($item) => [
        'id' => (int) $item->id_experiencia,
        'empresa' => (string) $item->empresa,
        'cargo' => (string) $item->cargo,
        'desde' => $item->desde?->format('Y-m-d'),
        'hasta' => $item->hasta?->format('Y-m-d'),
        'trabajo_actual' => (bool) $item->trabajo_actual,
    ])->values();
@endphp

@section('title', $esMiPerfil ? 'Mi experiencia | Facilitadores FEPADE' : 'Experiencia laboral | Facilitadores FEPADE')
@section('page-title', $esMiPerfil ? 'Mi experiencia laboral' : 'Experiencia laboral')
@section('page-subtitle', $esMiPerfil ? 'Construye tu trayectoria profesional paso a paso.' : 'Gestiona la trayectoria profesional del consultor.')

@section('content')
@include('fac.consultores.partials._fac_input_ux')

@include('fac.consultores.partials._wizard', ['step' => 3, 'consultor' => $consultor])
@if(session('experiencia_aviso_duplicado'))
    <div class="alert alert-warning" role="status">
        La experiencia se guardó, pero encontramos otro registro con la misma institución, cargo y período.
        Revisa la línea del tiempo y elimina alguno si fue agregado por error.
    </div>
@endif

<div class="perfil-panel mb-4 experiencia-pagina">
    <div class="experiencia-encabezado">
        <div>
            <h2 class="h4 mb-2">{{ $esMiPerfil ? 'Construye tu historia laboral' : 'Trayectoria laboral del consultor' }}</h2>
            <p class="text-muted mb-0">Agrega tus experiencias una por una. Puedes comenzar por tu trabajo más reciente; las ordenaremos automáticamente por fecha.</p>
        </div>
        <button type="button" class="btn btn-fepade experiencia-btn" onclick="mostrarFormularioPerfil('crear-experiencia')" aria-controls="crear-experiencia" aria-expanded="false">
            + Añadir experiencia
        </button>
    </div>

    <div class="experiencia-resumen" aria-label="Resumen de experiencias laborales">
        <div class="experiencia-resumen-item"><strong>{{ $totalExperiencias }}</strong><span>{{ $totalExperiencias === 1 ? 'Experiencia registrada' : 'Experiencias registradas' }}</span></div>
        <div class="experiencia-resumen-item"><strong>{{ $trabajosActuales }}</strong><span>{{ $trabajosActuales === 1 ? 'Trabajo actual' : 'Trabajos actuales' }}</span></div>
    </div>

    <div id="crear-experiencia" class="perfil-form-wrapper d-none experiencia-editor" tabindex="-1">
        @include('fac.consultores.partials._experiencia_laboral_form', ['consultor' => $consultor, 'experiencia' => null])
    </div>

    <div class="experiencia-listado-titulo">
        <h3 class="h5 mb-1">Línea del tiempo profesional</h3>
        <p class="text-muted mb-0">De la experiencia más reciente a la más antigua. Puedes editar cada registro cuando lo necesites.</p>
    </div>

    @if($totalExperiencias > 0)
        <div class="experiencia-timeline" aria-label="Experiencias laborales ordenadas cronológicamente">
            @foreach($experiencias as $experiencia)
                <article class="experiencia-timeline-item">
                    <span class="experiencia-timeline-punto" aria-hidden="true"></span>
                    <div class="experiencia-timeline-card">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div class="flex-grow-1">
                                @if($experiencia->trabajo_actual)
                                    <span class="experiencia-actual">● Trabajo actual</span>
                                @endif
                                <h4 class="h5 mb-1">{{ $experiencia->cargo ?: 'Cargo no registrado' }}</h4>
                                <p class="mb-2 fw-semibold">{{ $experiencia->empresa ?: 'Empresa no registrada' }}</p>
                                <p class="experiencia-periodo mb-0">
                                    <span aria-hidden="true">📅</span>
                                    {{ $experiencia->desde ? ucfirst($experiencia->desde->translatedFormat('F Y')) : 'Inicio sin registrar' }}
                                    —
                                    {{ $experiencia->trabajo_actual ? 'Actualidad' : ($experiencia->hasta ? ucfirst($experiencia->hasta->translatedFormat('F Y')) : 'Fin sin registrar') }}
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 experiencia-btn" onclick="mostrarFormularioPerfil('editar-experiencia-{{ $experiencia->id_experiencia }}')" aria-controls="editar-experiencia-{{ $experiencia->id_experiencia }}" aria-expanded="false"><i class="fa-solid fa-pen" aria-hidden="true"></i><span>Editar</span></button>
                                <form method="POST" action="{{ route('fac.consultores.experiencia.laboral.destroy', [$consultor, $experiencia]) }}" onsubmit="return confirm('¿Deseas eliminar esta experiencia laboral? Esta acción no se puede deshacer desde esta pantalla.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 experiencia-btn"><i class="fa-solid fa-trash" aria-hidden="true"></i><span>Eliminar</span></button>
                                </form>
                            </div>
                        </div>
                        @if($experiencia->descripcion)
                            <div class="experiencia-detalle"><strong>Funciones realizadas</strong><p class="mb-0 mt-1" style="white-space: pre-line">{{ $experiencia->descripcion }}</p></div>
                        @endif
                        @if($experiencia->jefe_nombre || $experiencia->jefe_email || $experiencia->jefe_telefono || $experiencia->url_evidencia)
                            <div class="experiencia-detalle">
                                @if($experiencia->jefe_nombre)<div><strong>Jefe inmediato:</strong> {{ $experiencia->jefe_nombre }}</div>@endif
                                @if($experiencia->jefe_email)<div><strong>Correo:</strong> {{ $experiencia->jefe_email }}</div>@endif
                                @if($experiencia->jefe_telefono)<div><strong>Teléfono:</strong> {{ $experiencia->jefe_telefono }}</div>@endif
                                @if($experiencia->url_evidencia)<div class="mt-2"><a href="{{ \Illuminate\Support\Facades\Storage::url($experiencia->url_evidencia) }}" target="_blank" rel="noopener noreferrer">Ver evidencia laboral (abre otra pestaña)</a></div>@endif
                            </div>
                        @endif
                        <div id="editar-experiencia-{{ $experiencia->id_experiencia }}" class="perfil-form-wrapper d-none experiencia-editor mt-3" tabindex="-1">
                            @include('fac.consultores.partials._experiencia_laboral_form', ['consultor' => $consultor, 'experiencia' => $experiencia])
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="experiencia-vacio" role="status">
            <div class="experiencia-vacio-icono" aria-hidden="true">💼</div>
            <h4 class="h5">Todavía no hay experiencias registradas</h4>
            <p class="text-muted">Puedes comenzar por tu empleo actual, tu trabajo más reciente o tu primera experiencia laboral.</p>
            <button type="button" class="btn btn-fepade experiencia-btn" onclick="mostrarFormularioPerfil('crear-experiencia')">+ Registrar mi primera experiencia</button>
        </div>
    @endif
</div>

@include('fac.consultores.partials._wizard_actions', [
    'consultor' => $consultor,
    'anterior' => route('fac.consultores.contacto.edit', $consultor),
    'continuarRoute' => route('fac.consultores.experiencia.continuar', $consultor),
    'continuarLabel' => 'Continuar a Formación',
])
@endsection

@push('styles')
<style>
/* Diseño guiado compartido con Formación V2 */
.experiencia-pagina .fac-guide-progress{height:7px;background:#e9edf2;border-radius:99px;overflow:hidden}
.experiencia-pagina .fac-guide-progress-fill{display:block;width:33.333%;height:100%;background:#00bf92;transition:width .2s}
.experiencia-pagina .fac-guide-step .form-label{font-weight:600}
.experiencia-pagina .fac-guide-step .form-text{font-size:.82rem}
.experiencia-pagina .fac-upload-box{display:flex;align-items:center;gap:12px;flex-wrap:wrap;border:1px dashed #cbd5e1;border-radius:10px;padding:14px;background:#f8fafc}
.experiencia-pagina .fac-upload-button{white-space:nowrap;cursor:pointer}
.experiencia-pagina .fac-upload-filename{overflow-wrap:anywhere}
.experiencia-pagina .js-exp-resumen[hidden]{display:none!important}
.experiencia-pagina .js-experiencia-form{border:0!important;padding:0!important;margin-bottom:0!important;background:transparent!important}
.experiencia-pagina .experiencia-editor{background:#fff;border:1px solid #d9e1e7}

.experiencia-pagina { color: #0f1a20; }
.experiencia-encabezado { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:1.25rem; }
.experiencia-eyebrow { display:block; color:#467599; font-size:.78rem; font-weight:700; letter-spacing:.05em; margin-bottom:.55rem; }
.experiencia-encabezado p { max-width:720px; line-height:1.6; }
.experiencia-btn { min-height:44px; display:inline-flex; align-items:center; justify-content:center; padding:.55rem 1rem; font-weight:600; }
.experiencia-resumen { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:1rem; margin:1.5rem 0; }
.experiencia-resumen-item { border:1px solid #d9e1e7; border-radius:12px; background:#f7fafc; padding:1.1rem 1.25rem; display:flex; flex-direction:column; gap:.25rem; }
.experiencia-resumen-item strong { font-size:1.75rem; color:#294e68; line-height:1.1; }
.experiencia-resumen-item span { color:#465660; }
.experiencia-listado-titulo { margin:1.5rem 0 1.25rem; }
.experiencia-timeline { position:relative; padding-left:2.25rem; }
.experiencia-timeline::before { content:''; position:absolute; left:.65rem; top:1.1rem; bottom:1.2rem; width:3px; background:#d2dfe8; border-radius:4px; }
.experiencia-timeline-item { position:relative; margin-bottom:1.25rem; }
.experiencia-timeline-punto { position:absolute; width:16px; height:16px; background:#467599; border:3px solid white; box-shadow:0 0 0 2px #467599; border-radius:50%; left:-1.98rem; top:1.35rem; }
.experiencia-timeline-card { border:1px solid #d9e1e7; border-radius:14px; padding:1.25rem; background:#fff; box-shadow:0 2px 8px rgba(15,26,32,.04); }
.experiencia-actual { display:inline-block; border-radius:20px; background:#e5f5ee; color:#146c43; padding:.2rem .65rem; font-size:.8rem; font-weight:700; margin-bottom:.65rem; }
.experiencia-periodo { color:#526675; font-size:.94rem; }
.experiencia-detalle { border-top:1px solid #e6edf2; margin-top:1rem; padding-top:1rem; line-height:1.6; overflow-wrap:anywhere; }
.experiencia-editor { border:1px solid #b7cedf; border-radius:12px; padding:1rem; margin:1rem 0; background:#f8fbfd; }
.experiencia-vacio { text-align:center; border:2px dashed #d4e0e8; border-radius:14px; padding:2.5rem 1rem; }
.experiencia-vacio-icono { font-size:2rem; margin-bottom:.5rem; }
.experiencia-vacio p { max-width:530px; margin:.5rem auto 1.2rem; }
@media(max-width:575.98px) { .experiencia-resumen { grid-template-columns:1fr; } .experiencia-encabezado > .btn { width:100%; } .experiencia-timeline { padding-left:1.5rem; } .experiencia-timeline::before { left:.3rem; } .experiencia-timeline-punto { left:-1.57rem; } .experiencia-timeline-card { padding:1rem; } }
</style>
@endpush

@push('scripts')
<script>
window.experienciasLaboralesComparacion = @json($experienciasComparacion);
function mostrarFormularioPerfil(id) {
    document.querySelectorAll('.perfil-form-wrapper').forEach(function (el) { el.classList.add('d-none'); });
    document.querySelectorAll('[aria-controls^="crear-experiencia"], [aria-controls^="editar-experiencia-"]').forEach(function (el) { el.setAttribute('aria-expanded', 'false'); });
    const destino = document.getElementById(id);
    if (!destino) return;
    destino.classList.remove('d-none');
    document.querySelectorAll('[aria-controls="' + id + '"]').forEach(function (el) { el.setAttribute('aria-expanded', 'true'); });
    destino.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'start' });
    destino.focus({ preventScroll: true });
}
function cerrarFormulariosPerfil() {
    document.querySelectorAll('.perfil-form-wrapper').forEach(function (el) { el.classList.add('d-none'); });
    document.querySelectorAll('[aria-controls^="crear-experiencia"], [aria-controls^="editar-experiencia-"]').forEach(function (el) { el.setAttribute('aria-expanded', 'false'); });
}
</script>
@endpush
