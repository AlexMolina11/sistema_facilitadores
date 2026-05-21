@extends('layouts.app')

@section('title', 'Experiencia consultor | Facilitadores FEPADE')
@section('page-title', 'Experiencia del consultor')
@section('page-subtitle', 'Experiencia laboral, habilidades e idiomas')

@section('content')

<x-ui.page-header 
    title="Experiencia del consultor"
    subtitle="{{ $consultor->nombre_completo }}"
/>

@include('fac.consultores.partials._wizard', ['step' => 4, 'consultor' => $consultor])

<form method="POST" action="{{ route('fac.consultores.experiencia.update', $consultor) }}">
    @csrf

    <div class="fepade-card">
        <h5 class="mb-3">Experiencia y competencias</h5>

        <div class="alert alert-info mb-0">
            Esta sección ya está conectada al flujo del wizard. En el siguiente paso agregaremos experiencia laboral, idiomas, habilidades, referencias, disponibilidad y tipos de consultoría.
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('fac.consultores.formacion.edit', $consultor) }}" class="btn btn-outline-secondary">
            Volver a formación
        </a>

        <button type="submit" class="btn btn-fepade">
            Guardar y continuar
        </button>
    </div>
</form>

@endsection