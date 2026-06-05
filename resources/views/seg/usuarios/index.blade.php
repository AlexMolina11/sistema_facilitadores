@extends('layouts.app')

@section('title', 'Usuarios | Facilitadores FEPADE')
@section('page-title', 'Usuarios')
@section('page-subtitle', 'Gestión de usuarios del sistema')

@section('content')

<x-ui.page-header title="Usuarios" subtitle="Administración de cuentas, roles y consultores asociados.">
    <a href="{{ route('seg.usuarios.create') }}" class="btn btn-fepade">
        Nuevo usuario
    </a>
</x-ui.page-header>

<div class="fepade-card mb-4">
    <form method="GET" action="{{ route('seg.usuarios.index') }}" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Buscar</label>
            <input type="text"
                   name="q"
                   value="{{ request('q') }}"
                   class="form-control"
                   placeholder="Nombre, apellido o correo">
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
            <a href="{{ route('seg.usuarios.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
        </div>
    </form>
</div>

<div class="fepade-card">
    <div class="table-responsive">
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
                @forelse($usuarios as $usuario)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $usuario->nombres }} {{ $usuario->apellidos }}</div>
                            <div class="text-muted small">ID: {{ $usuario->id_usuario }}</div>
                        </td>

                        <td>{{ $usuario->email }}</td>

                        <td>
                            @forelse($usuario->roles as $rol)
                                <span class="badge badge-primary-soft mb-1">
                                    {{ $rol->nombre }}
                                </span>
                            @empty
                                <span class="text-muted">Sin rol</span>
                            @endforelse
                        </td>

                        <td>
                            @if($usuario->consultor)
                                <div class="fw-semibold">{{ $usuario->consultor->nombres }} {{ $usuario->consultor->apellidos }}</div>
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
                            <div class="fepade-actions">
                                <a href="{{ route('seg.usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-primary">
                                    Editar
                                </a>

                                <form method="POST"
                                      action="{{ route('seg.usuarios.destroy', $usuario) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-outline-danger">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($usuarios->hasPages())
        <div class="mt-4 pagination-wrapper">
            {{ $usuarios->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
