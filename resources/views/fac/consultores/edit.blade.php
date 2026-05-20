@extends('layouts.app')

@section('title', 'Editar consultor | Facilitadores FEPADE')
@section('page-title', 'Editar consultor')
@section('page-subtitle', 'Actualización del expediente del consultor')

@section('content')

<x-ui.page-header 
    title="Editar consultor"
    subtitle="Actualiza la información base del consultor."
/>

@include('fac.consultores.partials._wizard', ['step' => 1])

<div class="fepade-card">
    <form method="POST" action="{{ route('fac.consultores.update', $consultor) }}">
        @csrf
        @method('PUT')

        @include('fac.consultores.partials._form', [
            'consultor' => $consultor,
            'catalogos' => $catalogos
        ])

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('fac.consultores.index') }}" class="btn btn-outline-secondary">
                Cancelar
            </a>

            <button type="submit" class="btn btn-fepade">
                Actualizar consultor
            </button>
        </div>
    </form>
</div>

@endsection