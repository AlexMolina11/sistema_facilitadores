@extends('layouts.app')

@section('title', 'Nuevo rol | Facilitadores FEPADE')
@section('page-title', 'Nuevo rol')
@section('page-subtitle', 'Creación de perfil de acceso')

@section('content')

<x-ui.page-header title="Nuevo rol" subtitle="Define el nombre del rol y los permisos que tendrá dentro del sistema." />

<form method="POST" action="{{ route('seg.roles.store') }}">
    @csrf

    <div class="card">
        <div class="card-body">
            @include('seg.roles.partials.form')
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('seg.roles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button class="btn btn-primary">Guardar</button>
        </div>
    </div>
</form>

@endsection
