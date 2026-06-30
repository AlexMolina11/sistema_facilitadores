@extends('layouts.app')

@section('title', 'Editar usuario | Facilitadores FEPADE')
@section('page-title', 'Editar usuario')
@section('page-subtitle', 'Actualizar cuenta de acceso')

@section('content')

<x-ui.page-header
    title="Editar usuario"
    subtitle="Modificación de datos, roles, permisos directos y consultor asociado."
/>

<form method="POST" action="{{ route('seg.usuarios.update', $usuario) }}">
    @csrf
    @method('PUT')

    <x-ui.page-card>
        @include('seg.usuarios.partials.form', [
            'usuario' => $usuario,
            'rolesSeleccionados' => $usuario->roles->pluck('id_rol')->toArray(),
            'permisosPermitidos' => $permisosPermitidos ?? [],
            'permisosDenegados' => $permisosDenegados ?? [],
            'permisosEfectivos' => $permisosEfectivos ?? [],
        ])

        <x-ui.form-actions
            :back-url="route('seg.usuarios.index')"
            back-text="Cancelar"
            submit-text="Actualizar usuario"
        />
    </x-ui.page-card>
</form>

@endsection