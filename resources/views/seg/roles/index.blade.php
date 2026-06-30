@extends('layouts.app')

@section('title', 'Roles | Facilitadores FEPADE')
@section('page-title', 'Roles')
@section('page-subtitle', 'Gestión de perfiles de acceso')

@section('content')

<x-ui.page-header title="Roles" subtitle="Administra los perfiles de acceso y los permisos asociados.">
    <a href="{{ route('seg.roles.create') }}" class="btn btn-fepade">
        <i class="fa-solid fa-user-shield me-1"></i>
        Nuevo rol
    </a>
</x-ui.page-header>

<x-ui.page-card class="mb-4">
    <form method="GET" action="{{ route('seg.roles.index') }}" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Buscar</label>
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                class="form-control"
                placeholder="Nombre o descripción"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">Estado</label>
            <select name="activo" class="form-select">
                <option value="">Todos</option>
                <option value="1" @selected(request('activo') === '1')>Activos</option>
                <option value="0" @selected(request('activo') === '0')>Inactivos</option>
            </select>
        </div>

        <div class="col-md-3 fepade-filter-actions">
            <button class="btn btn-navy w-100">
                <i class="fa-solid fa-filter me-1"></i>
                Filtrar
            </button>

            <a href="{{ route('seg.roles.index') }}" class="btn btn-outline-secondary w-100">
                Limpiar
            </a>
        </div>
    </form>
</x-ui.page-card>

<x-ui.table-card
    title="Roles registrados"
    subtitle="{{ $roles->total() }} registro(s) encontrado(s)"
    :items="$roles"
    empty-title="No hay roles registrados."
    empty-message="No se encontraron perfiles de acceso."
>
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Rol</th>
                <th>Descripción</th>
                <th>Usuarios</th>
                <th>Permisos</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @foreach($roles as $rol)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $rol->nombre }}</div>
                        <div class="text-muted small">ID: {{ $rol->id_rol }}</div>
                    </td>

                    <td>
                        {{ $rol->descripcion ?? 'Sin descripción' }}
                    </td>

                    <td>
                        <span class="badge badge-muted-soft">
                            {{ $rol->usuarios_count }}
                        </span>
                    </td>

                    <td>
                        <span class="badge badge-module-soft">
                            {{ $rol->permisos_count }}
                        </span>
                    </td>

                    <td>
                        @if($rol->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>

                    <td class="text-end">
                        <x-ui.action-buttons
                            :edit-url="route('seg.roles.edit', $rol)"
                            :delete-url="route('seg.roles.destroy', $rol)"
                            delete-message="¿Seguro que deseas eliminar este rol?"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection