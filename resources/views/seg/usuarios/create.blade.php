@extends('layouts.app')

@section('title', 'Nuevo usuario | Facilitadores FEPADE')
@section('page-title', 'Nuevo usuario')
@section('page-subtitle', 'Crear cuenta de acceso al sistema')

@section('content')

<x-ui.page-header
    title="Nuevo usuario"
    subtitle="Registro de usuarios internos del sistema con roles y permisos específicos."
/>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('seg.usuarios.store') }}">
            @csrf

            @include('seg.usuarios.partials.form', [
                'usuario' => null,
                'rolesSeleccionados' => [],
                'permisosPermitidos' => [],
                'permisosDenegados' => [],
                'permisosEfectivos' => [],
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('seg.usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button class="btn btn-primary">Guardar usuario</button>
            </div>
        </form>
    </div>
</div>

@endsection
