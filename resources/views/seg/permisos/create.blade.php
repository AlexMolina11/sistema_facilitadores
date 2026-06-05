@extends('layouts.app')

@section('title', 'Nuevo permiso | Facilitadores FEPADE')
@section('page-title', 'Nuevo permiso')
@section('page-subtitle', 'Creación de acción autorizable')

@section('content')

<x-ui.page-header title="Nuevo permiso" subtitle="Registra una nueva acción que podrá asignarse a roles." />

<form method="POST" action="{{ route('seg.permisos.store') }}">
    @csrf

    <div class="fepade-card">
        <div>
            @include('seg.permisos.partials.form')
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('seg.permisos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button class="btn btn-navy">Guardar</button>
        </div>
    </div>
</form>

@endsection
