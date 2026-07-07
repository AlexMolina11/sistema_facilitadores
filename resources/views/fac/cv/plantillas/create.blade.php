@extends('layouts.app')

@section('title', 'Nueva plantilla CV | Facilitadores FEPADE')
@section('page-title', 'Nueva plantilla CV')
@section('page-subtitle', 'Registrar una nueva plantilla para exportación de CV.')

@section('content')

<x-ui.page-header title="Nueva plantilla CV" subtitle="El archivo Blade se resolverá automáticamente desde el código.">
    <a href="{{ route('fac.catalogos.cv-plantillas.index') }}" class="btn btn-outline-secondary">
        Volver
    </a>
</x-ui.page-header>

@include('fac.cv.plantillas.partials.form', [
    'plantilla' => $plantilla,
    'action' => route('fac.catalogos.cv-plantillas.store'),
    'method' => 'POST',
])

@endsection