@extends('layouts.app')

@section('title', 'Permisos | Facilitadores FEPADE')
@section('page-title', 'Permisos')
@section('page-subtitle', 'Catálogo de acciones autorizables')

@section('content')

<x-ui.page-header title="Permisos" subtitle="Administra las acciones que pueden asignarse a los roles del sistema.">
    <a href="{{ route('seg.permisos.create') }}" class="btn btn-fepade">
        <i class="fa-solid fa-key me-1"></i>
        Nuevo permiso
    </a>
</x-ui.page-header>

<x-ui.page-card class="mb-4">
    <form method="GET" action="{{ route('seg.permisos.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Buscar</label>
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                class="form-control"
                placeholder="Código, nombre, descripción o módulo"
            >
        </div>

        <div class="col-md-2">
            <label class="form-label">Módulo</label>
            <select name="modulo" class="form-select">
                <option value="">Todos</option>
                @foreach($modulos as $modulo)
                    <option value="{{ $modulo }}" @selected(request('modulo') === $modulo)>
                        {{ $modulo }}
                    </option>
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
            <button class="btn btn-navy w-100">
                <i class="fa-solid fa-filter me-1"></i>
                Filtrar
            </button>

            <a href="{{ route('seg.permisos.index') }}" class="btn btn-outline-secondary w-100">
                Limpiar
            </a>
        </div>
    </form>
</x-ui.page-card>

<x-ui.table-card
    title="Permisos registrados"
    subtitle="{{ $permisos->total() }} registro(s) encontrado(s)"
    :items="$permisos"
    empty-title="No hay permisos registrados."
    empty-message="No se encontraron acciones autorizables."
>
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Módulo</th>
                <th>Roles</th>
                <th>Permisos directos</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @foreach($permisos as $permiso)
                <tr>
                    <td>
                        <code class="fepade-code">{{ $permiso->codigo }}</code>
                    </td>

                    <td>
                        <div class="fw-semibold">{{ $permiso->nombre }}</div>

                        @if($permiso->descripcion)
                            <div class="text-muted small">
                                {{ $permiso->descripcion }}
                            </div>
                        @endif
                    </td>

                    <td>
                        <span class="badge badge-module-soft">
                            {{ $permiso->modulo ?? 'General' }}
                        </span>
                    </td>

                    <td>
                        <span class="badge badge-muted-soft">
                            {{ $permiso->roles_count }}
                        </span>
                    </td>

                    <td>
                        <span class="badge badge-muted-soft">
                            {{ $permiso->usuarios_directos_count }}
                        </span>
                    </td>

                    <td>
                        @if($permiso->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>

                    <td class="text-end">
                        <x-ui.action-buttons
                            :edit-url="route('seg.permisos.edit', $permiso)"
                            :delete-url="route('seg.permisos.destroy', $permiso)"
                            delete-message="¿Seguro que deseas eliminar este permiso?"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection