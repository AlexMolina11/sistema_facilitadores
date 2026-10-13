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
        ? 'Mis referencias | Facilitadores FEPADE'
        : 'Referencias | Facilitadores FEPADE'
)

@section(
    'page-title',
    $esMiPerfil
        ? 'Mis referencias'
        : 'Referencias del consultor'
)

@section(
    'page-subtitle',
    $esMiPerfil
        ? 'Administra las referencias personales y profesionales de tu expediente.'
        : 'Administra las referencias personales y profesionales del consultor.'
)

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/intl-tel-input/css/intlTelInput.min.css') }}">
<style>
    .referencia-form .iti { width: 100%; }
    .referencia-form .iti input { width: 100%; }
</style>
@endpush

@section('content')

<x-ui.page-header
    :title="$esMiPerfil
        ? 'Mis referencias'
        : 'Referencias'"
    :subtitle="$esMiPerfil
        ? 'Registra contactos que puedan respaldar tu trayectoria personal o profesional.'
        : 'Registra contactos de referencia asociados al expediente del consultor.'"
/>

@include('fac.consultores.partials._wizard', ['step' => 7, 'consultor' => $consultor])

 @foreach($catalogos['tiposReferencia'] as $tipoReferencia)
    <div class="perfil-panel mb-4">
        @php $items = $referenciasPorTipo->get($tipoReferencia->id_tipo_referencia, collect()); @endphp
        <section class="mb-5">
            <div class="perfil-section-header">
                <div><h4>{{ $tipoReferencia->nombre }}</h4><p class="text-muted mb-0">Máximo 3 referencias para este tipo. Registradas: {{ $items->count() }}/3.</p></div>
                @if($items->count() < 3)
                    <button 
                        type="button" 
                        class="btn btn-fepade" 
                        onclick="mostrarFormularioPerfil('crear-referencia-{{ $tipoReferencia->id_tipo_referencia }}')"
                    >
                        + Añadir referencia
                    </button>
                @else
                    <span class="badge badge-warning-soft">
                        Límite alcanzado
                    </span>
                @endif
            </div>

            <div id="crear-referencia-{{ $tipoReferencia->id_tipo_referencia }}" class="perfil-form-wrapper d-none">
                @include('fac.consultores.partials._referencia_form', ['consultor' => $consultor, 'catalogos' => $catalogos, 'tipoReferencia' => $tipoReferencia, 'referencia' => null])
            </div>

            @forelse($items as $referencia)
                @php $relacion = $catalogos['tiposRelacion']->firstWhere('id_tipo_relacion', $referencia->id_tipo_relacion); @endphp
                <article class="perfil-card-item">
                    <div class="perfil-card-icon">👤</div>
                    <div class="perfil-card-body">
                        <div class="d-flex justify-content-between gap-3">
                            <div>
                                <div class="perfil-card-title">{{ $referencia->nombre }}</div>
                                @if($relacion)<div class="perfil-card-subtitle">Relación: {{ $relacion->nombre }}</div>@endif
                                @if($referencia->cargo || $referencia->empresa)<div class="perfil-card-subtitle">{{ $referencia->cargo }} @if($referencia->empresa) · {{ $referencia->empresa }} @endif</div>@endif
                                <div class="perfil-card-meta">@if($referencia->telefono) {{ $referencia->telefono }} @endif @if($referencia->correo) · {{ $referencia->correo }} @endif</div>
                            </div>
                            <div class="d-flex gap-2"><button type="button" class="btn btn-sm btn-link text-secondary" onclick="mostrarFormularioPerfil('editar-referencia-{{ $referencia->id_referencia }}')">✎</button><form method="POST" action="{{ route('fac.consultores.referencias.destroy', [$consultor, $referencia]) }}" onsubmit="return confirm('¿Deseas eliminar esta referencia?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-link text-danger">🗑</button></form></div>
                        </div>
                        <div id="editar-referencia-{{ $referencia->id_referencia }}" class="perfil-form-wrapper d-none mt-3">
                            @include('fac.consultores.partials._referencia_form', ['consultor' => $consultor, 'catalogos' => $catalogos, 'tipoReferencia' => $tipoReferencia, 'referencia' => $referencia])
                        </div>
                    </div>
                </article>
            @empty
                <div class="text-muted border rounded p-4 mb-3">No hay referencias registradas para este tipo.</div>
            @endforelse
        </section>
    </div>
@endforeach

@include(
    'fac.consultores.partials._wizard_actions',
    [
        'consultor' =>
            $consultor,

        'anterior' =>
            route(
                'fac.consultores.idiomas.edit',
                $consultor
            ),

        'continuarRoute' =>
            route(
                'fac.consultores.referencias.continuar',
                $consultor
            ),

        'continuarLabel' =>
            'Continuar a Disponibilidad',
    ]
)
@endsection
@push('scripts')
<script src="{{ asset('vendor/intl-tel-input/js/intlTelInputWithUtils.min.js') }}"></script>
<script>
function mostrarFormularioPerfil(id) {
    document.querySelectorAll('.perfil-form-wrapper').forEach(el => el.classList.add('d-none'));
    const target = document.getElementById(id);
    if (target) {
        target.classList.remove('d-none');
        target.scrollIntoView({behavior: 'smooth', block: 'nearest'});
    }
}
function cerrarFormulariosPerfil() {
    document.querySelectorAll('.perfil-form-wrapper').forEach(el => el.classList.add('d-none'));
}
document.addEventListener('DOMContentLoaded', function () {
    const telefonos = new Map();
    document.querySelectorAll('.referencia-form [data-referencia-telefono]').forEach(input => {
        if (typeof window.intlTelInput !== 'function') return;
        const iti = window.intlTelInput(input, {
            initialCountry: 'sv',
            countryOrder: ['sv', 'gt', 'hn', 'ni', 'cr', 'pa', 'us', 'mx'],
            nationalMode: true,
            autoPlaceholder: 'polite',
            formatAsYouType: true
        });
        telefonos.set(input, iti);
        if (input.value.trim().startsWith('+')) iti.setNumber(input.value.trim());
    });

    document.querySelectorAll('.referencia-form').forEach(form => {
        form.addEventListener('submit', function (event) {
            const input = form.querySelector('[data-referencia-telefono]');
            const iti = input && telefonos.get(input);
            if (!input || !iti || !input.value.trim()) return;

            if (!iti.isValidNumber()) {
                event.preventDefault();
                input.setCustomValidity('Ingresa un número telefónico válido para el país seleccionado.');
                input.reportValidity();
                input.addEventListener('input', () => input.setCustomValidity(''), {once: true});
                return;
            }
            input.setCustomValidity('');
            input.value = iti.getNumber();
        });
    });

    const formularioConErrores = @json(old('_referencia_form_id', ''));
    const hayErrores = @json($errors->any());
    if (hayErrores && formularioConErrores) {
        mostrarFormularioPerfil(formularioConErrores);
    }
});
</script>
@endpush
