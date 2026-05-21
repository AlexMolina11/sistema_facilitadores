@extends('layouts.app')

@section('title', 'Departamentos | Facilitadores FEPADE')
@section('page-title', 'Catálogo de departamentos')
@section('page-subtitle', 'Administración de departamentos disponibles en el sistema')

@section('content')
<x-ui.page-header title="Departamentos" subtitle="Listado de departamentos registrados en el sistema.">
    <a href="{{ route('fac.catalogos.departamentos.create') }}" class="btn btn-fepade">
        Nuevo departamento
    </a>
</x-ui.page-header>

<div class="fepade-card mb-4">
    <form method="GET" action="{{ route('fac.catalogos.departamentos.index') }}" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Buscar</label>
            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                class="form-control"
                placeholder="Buscar por nombre, código MH o país"
            >
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-navy w-100">Buscar</button>
        </div>
        <div class="col-md-3">
            <a href="{{ route('fac.catalogos.departamentos.index') }}" class="btn btn-outline-secondary w-100">
                Limpiar
            </a>
        </div>
    </form>
</div>

<div class="fepade-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>País</th>
                    <th>Nombre</th>
                    <th>Código MH</th>
                    <th>Georeferencia</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departamentos as $depto)
                <tr>
                    <td>{{ $depto->pais->nombre_pais ?? '—' }}</td>
                    <td class="fw-semibold">{{ $depto->nombre_departamento }}</td>
                    <td>{{ $depto->mh_codigo_depto ?? '—' }}</td>
                    <td>{{ $depto->georeferencia ?? '—' }}</td>
                    <td>
                        @if($depto->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('fac.catalogos.departamentos.edit', $depto->id_departamento) }}"
                           class="btn btn-sm btn-outline-secondary">
                            Editar
                        </a>
                        <form
                            action="{{ route('fac.catalogos.departamentos.destroy', $depto->id_departamento) }}"
                            method="POST"
                            class="d-inline"
                            onsubmit="return confirm('¿Deseas desactivar este departamento?')"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        No hay departamentos registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 pagination-wrapper">
        {{ $departamentos->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection