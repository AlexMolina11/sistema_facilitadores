@extends('layouts.app')

@section('title', 'Permisos | Facilitadores FEPADE')
@section('page-title', 'Permisos')
@section('page-subtitle', 'Catálogo de acciones autorizables')

@section('content')

<x-ui.page-header
    title="Permisos"
    subtitle="Administra las acciones que pueden asignarse a los roles del sistema."
/>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Buscar</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Código, nombre o descripción">
            </div>

            <div class="col-md-2">
                <label class="form-label">Módulo</label>
                <select name="modulo" class="form-select">
                    <option value="">Todos</option>
                    @foreach($modulos as $modulo)
                        <option value="{{ $modulo }}" @selected(request('modulo') === $modulo)>{{ $modulo }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
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
                <a href="{{ route('seg.permisos.create') }}" class="btn btn-success w-100">Nuevo</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Módulo</th>
                    <th>Roles</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permisos as $permiso)
                    <tr>
                        <td><code>{{ $permiso->codigo }}</code></td>
                        <td>
                            <strong>{{ $permiso->nombre }}</strong>
                            @if($permiso->descripcion)
                                <div class="text-muted small">{{ $permiso->descripcion }}</div>
                            @endif
                        </td>
                        <td><span class="badge bg-dark">{{ $permiso->modulo ?? 'General' }}</span></td>
                        <td>{{ $permiso->roles_count }}</td>
                        <td>
                            @if($permiso->activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('seg.permisos.edit', $permiso) }}" class="btn btn-sm btn-outline-primary">Editar</a>

                            <form method="POST" action="{{ route('seg.permisos.destroy', $permiso) }}" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este permiso?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay permisos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($permisos->hasPages())
        <div class="card-footer">{{ $permisos->links() }}</div>
    @endif
</div>

@endsection
