@extends('layouts.app')

@section('title', 'Editar rol | Facilitadores FEPADE')
@section('page-title', 'Editar rol')
@section('page-subtitle', 'Actualización de perfil de acceso')

@section('content')

<x-ui.page-header title="Editar rol" subtitle="Actualiza los datos del rol y sus permisos asociados." />

<form method="POST" action="{{ route('seg.roles.update', $rol) }}">
    @csrf
    @method('PUT')

    <div class="fepade-card">
        <div>
            @include('seg.roles.partials.form')
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('seg.roles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button class="btn btn-navy">Actualizar</button>
        </div>
    </div>
</form>

@endsection
