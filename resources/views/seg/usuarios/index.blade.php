@extends('layouts.app')

@section('title', 'Usuarios | Facilitadores FEPADE')
@section('page-title', 'Usuarios')
@section('page-subtitle', 'Gestión de usuarios del sistema')

@section('content')

<x-ui.page-header title="Usuarios" subtitle="Administración de cuentas, roles y consultores asociados.">
    <a href="{{ route('seg.usuarios.create') }}" class="btn btn-fepade">
        <i class="fa-solid fa-user-plus me-1"></i>
        Nuevo usuario
    </a>
</x-ui.page-header>

<x-ui.page-card class="mb-4">
    <form method="GET" action="{{ route('seg.usuarios.index') }}" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Buscar</label>
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                class="form-control"
                placeholder="Nombre, apellido o correo"
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

            <a href="{{ route('seg.usuarios.index') }}" class="btn btn-outline-secondary w-100">
                Limpiar
            </a>
        </div>
    </form>
</x-ui.page-card>

<x-ui.table-card
    title="Usuarios registrados"
    subtitle="{{ $usuarios->total() }} registro(s) encontrado(s)"
    :items="$usuarios"
    empty-title="No hay usuarios registrados."
    empty-message="No se encontraron cuentas de acceso."
>
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Roles</th>
                <th>Consultor asociado</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @foreach($usuarios as $usuario)
                <tr>
                    <td>
                        <div class="fw-semibold">
                            {{ $usuario->nombres }} {{ $usuario->apellidos }}
                        </div>
                        <div class="text-muted small">
                            ID: {{ $usuario->id_usuario }}
                        </div>
                    </td>

                    <td>
                        <code class="fepade-code">
                            {{ $usuario->email }}
                        </code>
                    </td>

                    <td>
                        @forelse($usuario->roles as $rol)
                            <span class="badge badge-primary-soft mb-1">
                                {{ $rol->nombre }}
                            </span>
                        @empty
                            <span class="badge badge-muted-soft">
                                Sin rol
                            </span>
                        @endforelse
                    </td>

                    <td>
                        @if($usuario->consultor)
                            <div class="fw-semibold">
                                {{ $usuario->consultor->nombres }} {{ $usuario->consultor->apellidos }}
                            </div>
                        @else
                            <span class="text-muted">No asociado</span>
                        @endif
                    </td>

                    <td>
                        @if($usuario->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>

                    <td class="text-end">
                        <x-ui.action-buttons
                            :edit-url="route('seg.usuarios.edit', $usuario)"
                            :delete-url="route('seg.usuarios.destroy', $usuario)"
                            delete-message="¿Seguro que deseas eliminar este usuario?"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection