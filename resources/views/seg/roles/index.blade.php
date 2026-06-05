@extends('layouts.app')

@section('title', 'Roles | Facilitadores FEPADE')
@section('page-title', 'Roles')
@section('page-subtitle', 'Gestión de perfiles de acceso')

@section('content')

<x-ui.page-header
    title="Roles"
    subtitle="Administra los perfiles de acceso y los permisos asociados."
/>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
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

            <div class="col-md-2">
                <button class="btn btn-primary w-100">Filtrar</button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('seg.roles.create') }}" class="btn btn-success w-100">Nuevo</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
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
                        <td><strong>{{ $rol->nombre }}</strong></td>
                        <td>{{ $rol->descripcion ?? 'Sin descripción' }}</td>
                        <td>{{ $rol->usuarios_count }}</td>
                        <td>{{ $rol->permisos_count }}</td>
                        <td>
                            @if($rol->activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('seg.roles.edit', $rol) }}" class="btn btn-sm btn-outline-primary">Editar</a>

                            <form method="POST" action="{{ route('seg.roles.destroy', $rol) }}" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este rol?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
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
        <div class="card-footer">{{ $roles->links() }}</div>
    @endif
</div>

@endsection
