@extends('layouts.app')

@section('title', 'Editar permiso | Facilitadores FEPADE')
@section('page-title', 'Editar permiso')
@section('page-subtitle', 'Actualización de acción autorizable')

@section('content')

<x-ui.page-header title="Editar permiso" subtitle="Actualiza los datos del permiso seleccionado." />

<form method="POST" action="{{ route('seg.permisos.update', $permiso) }}">
    @csrf
    @method('PUT')

    <div class="fepade-card">
        <div>
            @include('seg.permisos.partials.form')
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('seg.permisos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button class="btn btn-navy">Actualizar</button>
        </div>
    </div>
</form>

@endsection
