@extends('layouts.app')

@php
    $esMiPerfil = filled(auth()->user()?->id_consultor)
        && (int) auth()->user()->id_consultor === (int) $consultor->id_consultor;
    $seleccionGuardada = $consultor->disponibilidades->first()?->id_tipo_disponibilidad;
    $seleccion = old('id_tipo_disponibilidad', $seleccionGuardada);
@endphp

@section('title', $esMiPerfil ? 'Mi disponibilidad | Facilitadores FEPADE' : 'Disponibilidad | Facilitadores FEPADE')
@section('page-title', $esMiPerfil ? 'Mi disponibilidad' : 'Editar disponibilidad')
@section('page-subtitle', $esMiPerfil ? 'Indica tu disponibilidad actual para participar en actividades de FEPADE.' : 'Selecciona la disponibilidad actual del consultor.')

@push('styles')
<style>
    .fac-disponibilidad-panel { background:#fff; border:1px solid #e0e6ec; border-radius:16px; padding:24px; box-shadow:0 5px 20px rgba(13,33,52,.035); }
    .fac-disponibilidad-title { font-size:1.35rem; font-weight:650; color:#102239; margin:0 0 5px; }
    .fac-disponibilidad-desc { color:#64748b; margin-bottom:0; }
    .fac-disponibilidad-options { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; margin-top:22px; }
    .fac-disponibilidad-option { display:flex; align-items:center; gap:13px; padding:18px; border:1.5px solid #dce4eb; border-radius:12px; cursor:pointer; min-height:82px; transition:border-color .15s,background-color .15s,box-shadow .15s; background:#fff; }
    .fac-disponibilidad-option:hover { border-color:#00b98b; background:#f7fffc; }
    .fac-disponibilidad-option:focus-within { outline:2px solid rgba(0,190,145,.32); outline-offset:2px; }
    .fac-disponibilidad-option.is-selected { border-color:#00bf92; background:#f0fbf7; box-shadow:0 0 0 1px rgba(0,191,146,.10); }
    .fac-disponibilidad-option input[type=radio] { flex:0 0 auto; width:19px; height:19px; accent-color:#00bf92; cursor:pointer; margin:0; }
    .fac-disponibilidad-option-name { font-weight:600; color:#14263a; line-height:1.4; }
    .fac-disponibilidad-option-check { margin-left:auto; color:#00a980; opacity:0; font-size:1.05rem; }
    .fac-disponibilidad-option.is-selected .fac-disponibilidad-option-check { opacity:1; }
    .fac-disponibilidad-note { display:flex; align-items:flex-start; gap:9px; background:#f4f8fb; border:1px solid #e1e8ee; border-radius:10px; padding:12px 14px; color:#56677a; font-size:.9rem; margin-top:18px; }
    .fac-disponibilidad-actions { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-top:22px; }
    .fac-disponibilidad-actions-left { display:flex; align-items:center; flex-wrap:wrap; gap:8px; }
    @media(max-width:767.98px) { .fac-disponibilidad-panel{padding:18px} .fac-disponibilidad-options{grid-template-columns:1fr} .fac-disponibilidad-actions{align-items:stretch} .fac-disponibilidad-actions .btn{white-space:normal} }
</style>
@endpush

@section('content')
@include('fac.consultores.partials._wizard', ['step' => 8, 'consultor' => $consultor])

<form method="POST" action="{{ route('fac.consultores.disponibilidad.continuar', $consultor) }}" id="facDisponibilidadForm">
    @csrf
    <section class="fac-disponibilidad-panel" aria-labelledby="facDisponibilidadTitulo">
        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
            <div>
                <h2 id="facDisponibilidadTitulo" class="fac-disponibilidad-title">Disponibilidad del consultor</h2>
                <p class="fac-disponibilidad-desc">{{ $esMiPerfil ? 'Selecciona la opción que mejor describe tu disponibilidad actual.' : 'Selecciona la opción que mejor describe la disponibilidad actual del consultor.' }}</p>
            </div>
            <span class="badge bg-light text-dark border">Última sección · 8 de 8</span>
        </div>

        <div class="fac-disponibilidad-options" role="radiogroup" aria-label="Situación de disponibilidad" @if($errors->has('id_tipo_disponibilidad')) aria-invalid="true" aria-describedby="facDisponibilidadError" @endif>
            @foreach($catalogos['tiposDisponibilidad'] as $tipo)
                @php $estaSeleccionada = (string) $seleccion === (string) $tipo->id_tipo_disponibilidad; @endphp
                <label class="fac-disponibilidad-option {{ $estaSeleccionada ? 'is-selected' : '' }}">
                    <input type="radio" name="id_tipo_disponibilidad" value="{{ $tipo->id_tipo_disponibilidad }}" {{ $estaSeleccionada ? 'checked' : '' }} required>
                    <span class="fac-disponibilidad-option-name">{{ $tipo->nombre }}</span>
                    <i class="fa-solid fa-circle-check fac-disponibilidad-option-check" aria-hidden="true"></i>
                </label>
            @endforeach
        </div>

        @error('id_tipo_disponibilidad')
            <div id="facDisponibilidadError" class="text-danger mt-3" role="alert"><i class="fa-solid fa-circle-exclamation me-1" aria-hidden="true"></i>{{ $message }}</div>
        @enderror

        <div class="fac-disponibilidad-note">
            <i class="fa-solid fa-circle-info mt-1" aria-hidden="true"></i>
            <span>Selecciona <strong>una sola opción</strong>. Puedes actualizar esta información posteriormente desde el expediente.</span>
        </div>
    </section>

    <div class="fac-disponibilidad-actions">
        <div class="fac-disponibilidad-actions-left">
            <a href="{{ route('fac.consultores.referencias.edit', $consultor) }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i>Anterior</a>
            <a href="{{ $esMiPerfil ? route('fac.mi-perfil') : route('fac.consultores.show', $consultor) }}" class="btn btn-outline-secondary"><i class="fa-solid fa-xmark me-1" aria-hidden="true"></i>{{ $esMiPerfil ? 'Salir a mi perfil' : 'Volver al expediente' }}</a>
        </div>
        <button type="submit" class="btn btn-fepade"><i class="fa-solid fa-circle-check me-1" aria-hidden="true"></i>{{ $esMiPerfil ? 'Finalizar y ver mi perfil' : 'Finalizar y ver expediente' }}</button>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const formulario = document.getElementById('facDisponibilidadForm');
    if (!formulario) return;
    const opciones = formulario.querySelectorAll('.fac-disponibilidad-option');
    formulario.addEventListener('change', function (event) {
        if (!event.target.matches('input[name="id_tipo_disponibilidad"]')) return;
        opciones.forEach(function (opcion) {
            const radio = opcion.querySelector('input[type="radio"]');
            opcion.classList.toggle('is-selected', Boolean(radio && radio.checked));
        });
    });
});
</script>
@endpush
@endsection
