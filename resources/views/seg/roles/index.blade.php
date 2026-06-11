@extends('layouts.app')

@section('title', 'Roles | Facilitadores FEPADE')
@section('page-title', 'Roles')
@section('page-subtitle', 'Gestión de perfiles de acceso')

@section('content')

<x-ui.page-header title="Roles" subtitle="Administra los perfiles de acceso y los permisos asociados.">
    <a href="{{ route('seg.roles.create') }}" class="btn btn-fepade">
        Nuevo rol
    </a>
</x-ui.page-header>

<div class="fepade-card mb-4">
    <form method="GET" action="{{ route('seg.roles.index') }}" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Buscar</label>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nombre o descripción">
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
            <button class="btn btn-navy w-100">Filtrar</button>
            <a href="{{ route('seg.roles.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
        </div>
    </form>
</div>

<div class="fepade-card">
    <div class="table-responsive">
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
                @forelse($roles as $rol)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $rol->nombre }}</div>
                            <div class="text-muted small">ID: {{ $rol->id_rol }}</div>
                        </td>
                        <td>{{ $rol->descripcion ?? 'Sin descripción' }}</td>
                        <td><span class="badge badge-muted-soft">{{ $rol->usuarios_count }}</span></td>
                        <td><span class="badge badge-module-soft">{{ $rol->permisos_count }}</span></td>
                        <td>
                            @if($rol->activo)
                                <span class="badge badge-success-soft">Activo</span>
                            @else
                                <span class="badge badge-warning-soft">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="fepade-actions">
                                <a href="{{ route('seg.roles.edit', $rol) }}" class="btn btn-sm btn-outline-primary">Editar</a>

                                <form method="POST" action="{{ route('seg.roles.destroy', $rol) }}" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este rol?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay roles registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($roles->hasPages())
        <div class="mt-4 pagination-wrapper">
            {{ $roles->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
