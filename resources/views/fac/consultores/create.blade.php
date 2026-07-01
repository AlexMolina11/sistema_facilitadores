@extends('layouts.app')

@section('title', 'Nuevo consultor | Facilitadores FEPADE')
@section('page-title', 'Nuevo consultor')
@section('page-subtitle', 'Registro visual tipo wizard del perfil del consultor')

@section('content')

<section class="busqueda-hero mb-4">
    <div class="busqueda-hero-main">
        <span class="busqueda-hero-icon">
            <i class="fa-solid fa-plus"></i>
        </span>

        <div>
            <h2>Nuevo consultor</h2>
            <p>Completa la información base del consultor.</p>
        </div>
    </div>

    <div class="busqueda-hero-actions">
        <a href="{{ route('fac.consultores.index') }}" class="btn btn-outline-light">
            <i class="fas fa-close me-1"></i> Cancelar
        </a>
    </div>
</section>

<div class="fepade-card">
    <form method="POST" action="{{ route('fac.consultores.store') }}" enctype="multipart/form-data">
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