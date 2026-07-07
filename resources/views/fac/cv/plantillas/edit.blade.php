@extends('layouts.app')

@section('title', 'Editar plantilla CV | Facilitadores FEPADE')
@section('page-title', 'Editar plantilla CV')
@section('page-subtitle', 'Actualizar información de la plantilla.')

@section('content')

<x-ui.page-header title="Editar plantilla CV" subtitle="{{ $plantilla->nombre }}">
    <a href="{{ route('fac.catalogos.cv-plantillas.index') }}" class="btn btn-outline-secondary">
        Volver
    </a>
</x-ui.page-header>

@include('fac.cv.plantillas.partials.form', [
    'plantilla' => $plantilla,
    'action' => route('fac.catalogos.cv-plantillas.update', $plantilla),
    'method' => 'PUT',
])

@endsection