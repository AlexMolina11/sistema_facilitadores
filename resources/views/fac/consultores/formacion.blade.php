@extends('layouts.app')

@section('title', 'Formación consultor | Facilitadores FEPADE')
@section('page-title', 'Formación del consultor')
@section('page-subtitle', 'Formación académica y atestados')

@section('content')

<x-ui.page-header 
    title="Formación del consultor"
    subtitle="{{ $consultor->nombre_completo }}"
/>

@include('fac.consultores.partials._wizard', ['step' => 3, 'consultor' => $consultor])

<form method="POST" action="{{ route('fac.consultores.formacion.update', $consultor) }}">
    @csrf

    <div class="fepade-card">
        <h5 class="mb-3">Formación académica</h5>

        <div class="alert alert-info mb-0">
            Esta sección ya está conectada al flujo del wizard. En el siguiente paso agregaremos el formulario funcional para formación académica y atestados.
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('fac.consultores.contacto.edit', $consultor) }}" class="btn btn-outline-secondary">
            Volver a contacto
        </a>

        <button type="submit" class="btn btn-fepade">
            Guardar y continuar
        </button>
    </div>
</form>

@endsection