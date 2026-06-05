@extends('layouts.app')

@section('title', 'Editar rol | Facilitadores FEPADE')
@section('page-title', 'Editar rol')
@section('page-subtitle', 'Actualización de perfil de acceso')

@section('content')

<x-ui.page-header title="Editar rol" subtitle="Actualiza los datos del rol y sus permisos asociados." />

<form method="POST" action="{{ route('seg.roles.update', $rol) }}">
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-body">
            @include('seg.roles.partials.form')
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('seg.roles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button class="btn btn-primary">Actualizar</button>
        </div>
    </div>
</form>

@endsection
