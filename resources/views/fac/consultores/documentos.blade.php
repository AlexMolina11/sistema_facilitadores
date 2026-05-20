@extends('layouts.app')

@section('title', 'Documentos consultor | Facilitadores FEPADE')
@section('page-title', 'Documentos del consultor')
@section('page-subtitle', 'Documentos adjuntos y expediente digital')

@section('content')

<x-ui.page-header 
    title="Documentos del consultor"
    subtitle="{{ $consultor->nombre_completo }}"
/>

@include('fac.consultores.partials._wizard', ['step' => 5, 'consultor' => $consultor])

<form method="POST" action="{{ route('fac.consultores.documentos.update', $consultor) }}">
    @csrf

    <div class="fepade-card">
        <h5 class="mb-3">Documentos adjuntos</h5>

        <div class="alert alert-info mb-0">
            Esta sección ya está conectada al flujo del wizard. En el siguiente paso agregaremos carga de documentos del consultor.
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('fac.consultores.experiencia.edit', $consultor) }}" class="btn btn-outline-secondary">
            Volver a experiencia
        </a>

        <button type="submit" class="btn btn-fepade">
            Finalizar expediente
        </button>
    </div>
</form>

@endsection