@extends('layouts.app')

@section('title', 'Nuevo usuario | Facilitadores FEPADE')
@section('page-title', 'Nuevo usuario')
@section('page-subtitle', 'Crear cuenta de acceso al sistema')

@section('content')

<x-ui.page-header
    title="Nuevo usuario"
    subtitle="Registro de usuarios internos del sistema con roles y permisos específicos."
/>

<form method="POST" action="{{ route('seg.usuarios.store') }}">
    @csrf

    <x-ui.page-card>
        @include('seg.usuarios.partials.form', [
            'usuario' => null,
            'rolesSeleccionados' => [],
            'permisosPermitidos' => [],
            'permisosDenegados' => [],
            'permisosEfectivos' => [],
        ])

        <x-ui.form-actions
            :back-url="route('seg.usuarios.index')"
            back-text="Cancelar"
            submit-text="Guardar usuario"
        />
    </x-ui.page-card>
</form>

@endsection