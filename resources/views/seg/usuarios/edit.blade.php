@extends('layouts.app')

@section('title', 'Editar usuario | Facilitadores FEPADE')
@section('page-title', 'Editar usuario')
@section('page-subtitle', 'Actualizar cuenta de acceso')

@section('content')

<x-ui.page-header
    title="Editar usuario"
    subtitle="Modificación de datos, roles, permisos directos y consultor asociado."
/>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('seg.usuarios.update', $usuario) }}">
            @csrf
            @method('PUT')

            @include('seg.usuarios.partials.form', [
                'usuario' => $usuario,
                'rolesSeleccionados' => $usuario->roles->pluck('id_rol')->toArray(),
                'permisosPermitidos' => $permisosPermitidos ?? [],
                'permisosDenegados' => $permisosDenegados ?? [],
                'permisosEfectivos' => $permisosEfectivos ?? [],
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('seg.usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button class="btn btn-primary">Actualizar usuario</button>
            </div>
        </form>
    </div>
</div>

@endsection
