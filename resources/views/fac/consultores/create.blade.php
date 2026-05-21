@extends('layouts.app')

@section('title', 'Nuevo consultor | Facilitadores FEPADE')
@section('page-title', 'Nuevo consultor')
@section('page-subtitle', 'Registro visual tipo wizard del perfil del consultor')

@section('content')

<x-ui.page-header 
    title="Nuevo consultor"
    subtitle="Completa la información base del consultor."
/>

@include('fac.consultores.partials._wizard', ['step' => 1, 'consultor' => null])

<div class="fepade-card">
    <form method="POST" action="{{ route('fac.consultores.store') }}">
        @csrf

        @include('fac.consultores.partials._form', [
            'consultor' => null,
            'catalogos' => $catalogos
        ])

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('fac.consultores.index') }}" class="btn btn-outline-secondary">
                Cancelar
            </a>

            <button type="submit" class="btn btn-fepade">
                Guardar consultor
            </button>
        </div>
    </form>
</div>

@endsection