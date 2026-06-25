@extends('layouts.app')

@section('title', 'Disponibilidad | Facilitadores FEPADE')
@section('page-title', 'Editar perfil')
@section('page-subtitle', 'Selecciona la disponibilidad del consultor.')

@section('content')

<x-ui.page-header
    title="Disponibilidad"
    subtitle="Cuéntanos sobre tu disponibilidad."
/>

@include('fac.consultores.partials._wizard', ['step' => 8, 'consultor' => $consultor])

<form method="POST" action="{{ route('fac.consultores.disponibilidad.continuar', $consultor) }}">
    @csrf
    <div class="perfil-panel mb-4">
        <div class="habilidad-panel">
            <h4>Disponibilidad</h4>
            <p class="text-muted">Selecciona una única situación de disponibilidad.</p>
            <div class="habilidad-grid">
                @foreach($catalogos['tiposDisponibilidad'] as $tipo)
                    <div class="form-check">
                        <input 
                            class="form-check-input" 
                            type="radio" 
                            name="id_tipo_disponibilidad" 
                            value="{{ $tipo->id_tipo_disponibilidad }}" 
                            id="disp_{{ $tipo->id_tipo_disponibilidad }}" 
                            {{ $consultor->disponibilidades->contains('id_tipo_disponibilidad', $tipo->id_tipo_disponibilidad) ? 'checked' : '' }}
                            required
                        >
                        <label class="form-check-label" for="disp_{{ $tipo->id_tipo_disponibilidad }}">{{ $tipo->nombre }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <a href="{{ route('fac.consultores.referencias.edit', $consultor) }}" class="btn btn-outline-secondary">Anterior: Referencias</a>
            <button type="submit" class="btn btn-fepade">Guardar y finalizar</button>
        </div>
    </div>
</form>

@endsection
