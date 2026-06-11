@extends('layouts.app')

@section('title', 'Permisos | Facilitadores FEPADE')
@section('page-title', 'Permisos')
@section('page-subtitle', 'Catálogo de acciones autorizables')

@section('content')

<x-ui.page-header title="Permisos" subtitle="Administra las acciones que pueden asignarse a los roles del sistema.">
    <a href="{{ route('seg.permisos.create') }}" class="btn btn-fepade">
        Nuevo permiso
    </a>
</x-ui.page-header>

<div class="fepade-card mb-4">
    <form method="GET" action="{{ route('seg.permisos.index') }}" class="row g-3 align-items-end">
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

        <div class="col-md-4 fepade-filter-actions">
            <button class="btn btn-navy w-100">Filtrar</button>
            <a href="{{ route('seg.permisos.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
        </div>
    </form>
</div>

<div class="fepade-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
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
                        <td><code class="fepade-code">{{ $permiso->codigo }}</code></td>
                        <td>
                            <div class="fw-semibold">{{ $permiso->nombre }}</div>
                            @if($permiso->descripcion)
                                <div class="text-muted small">{{ $permiso->descripcion }}</div>
                            @endif
                        </td>
                        <td><span class="badge badge-module-soft">{{ $permiso->modulo ?? 'General' }}</span></td>
                        <td><span class="badge badge-muted-soft">{{ $permiso->roles_count }}</span></td>
                        <td>
                            @if($permiso->activo)
                                <span class="badge badge-success-soft">Activo</span>
                            @else
                                <span class="badge badge-warning-soft">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="fepade-actions">
                                <a href="{{ route('seg.permisos.edit', $permiso) }}" class="btn btn-sm btn-outline-primary">Editar</a>

                                <form method="POST" action="{{ route('seg.permisos.destroy', $permiso) }}" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este permiso?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </div>
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
        <div class="mt-4 pagination-wrapper">
            {{ $permisos->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
