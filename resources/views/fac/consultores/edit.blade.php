@extends('layouts.app')

@section('title', 'Editar consultor | Facilitadores FEPADE')
@section('page-title', 'Editar consultor')
@section('page-subtitle', 'Actualización de datos personales del consultor')

@section('content')

<x-ui.page-header 
    title="Editar consultor"
    subtitle="Actualiza los datos personales, residencia, documentos y foto del consultor."
/>

@include('fac.consultores.partials._wizard', [
    'step' => 1, 
    'consultor' => $consultor
])

<div class="fepade-card">
    <form 
        method="POST" 
        action="{{ route('fac.consultores.update', $consultor) }}" 
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        @include('fac.consultores.partials._form', [
            'consultor' => $consultor,
            'catalogos' => $catalogos
        ])

        <hr class="my-4">

        <div class="d-flex justify-content-between gap-2">
            <a href="{{ route('fac.consultores.show', $consultor) }}" class="btn btn-outline-secondary">
                Volver al expediente
            </a>

            <div class="d-flex gap-2">
                <a href="{{ route('fac.consultores.index') }}" class="btn btn-outline-secondary">
                    Cancelar
                </a>

                <button type="submit" class="btn btn-fepade">
                    Actualizar datos personales
                </button>
            </div>
        </div>
    </form>
</div>

@endsection