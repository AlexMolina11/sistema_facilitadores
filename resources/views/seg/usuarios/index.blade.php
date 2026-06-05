@extends('layouts.app')

@section('title', 'Usuarios | Facilitadores FEPADE')
@section('page-title', 'Usuarios')
@section('page-subtitle', 'Gestión de usuarios del sistema')

@section('content')

<x-ui.page-header
    title="Usuarios"
    subtitle="Administración de cuentas, roles y consultores asociados."
/>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
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

            <div class="col-md-2">
                <button class="btn btn-primary w-100">
                    Filtrar
                </button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('seg.usuarios.create') }}" class="btn btn-success w-100">
                    Nuevo
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
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
                            <strong>{{ $usuario->nombres }} {{ $usuario->apellidos }}</strong>
                        </td>

                        <td>{{ $usuario->email }}</td>

                        <td>
                            @forelse($usuario->roles as $rol)
                                <span class="badge bg-primary">
                                    {{ $rol->nombre }}
                                </span>
                            @empty
                                <span class="text-muted">Sin rol</span>
                            @endforelse
                        </td>

                        <td>
                            @if($usuario->consultor)
                                {{ $usuario->consultor->nombres }} {{ $usuario->consultor->apellidos }}
                            @else
                                <span class="text-muted">No asociado</span>
                            @endif
                        </td>

                        <td>
                            @if($usuario->activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>

                        <td class="text-end">
                            <a href="{{ route('seg.usuarios.edit', $usuario) }}"
                               class="btn btn-sm btn-outline-primary">
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
        <div class="card-footer">
            {{ $usuarios->links() }}
        </div>
    @endif
</div>

@endsection