@extends('layouts.app')

@section('title', 'Municipios MH | Facilitadores FEPADE')
@section('page-title', 'Catálogo de municipios MH')
@section('page-subtitle', 'Administración de municipios MH disponibles en el sistema')

@section('content')
<x-ui.page-header title="Municipios MH" subtitle="Listado de municipios MH registrados en el sistema.">
    <a href="{{ route('fac.catalogos.municipios_mh.create') }}" class="btn btn-fepade">
        Nuevo municipio
    </a>
</x-ui.page-header>

<div class="fepade-card mb-4">
    <form method="GET" action="{{ route('fac.catalogos.municipios_mh.index') }}" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Buscar</label>
            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                class="form-control"
                placeholder="Buscar por nombre, código MH o departamento"
            >
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-navy w-100">Buscar</button>
        </div>
        <div class="col-md-3">
            <a href="{{ route('fac.catalogos.municipios_mh.index') }}" class="btn btn-outline-secondary w-100">
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
                    <th>Departamento</th>
                    <th>Nombre</th>
                    <th>Código MH</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($municipios as $municipio)
                <tr>
                    <td>{{ $municipio->departamento->nombre_departamento ?? '—' }}</td>
                    <td class="fw-semibold">{{ $municipio->municipio_mh_nombre }}</td>
                    <td>{{ $municipio->mh_codigo_municipio ?? '—' }}</td>
                    <td>
                        @if($municipio->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('fac.catalogos.municipios_mh.edit', $municipio->id_municipio_mh) }}"
                           class="btn btn-sm btn-outline-secondary">
                            Editar
                        </a>
                        <form
                            action="{{ route('fac.catalogos.municipios_mh.destroy', $municipio->id_municipio_mh) }}"
                            method="POST"
                            class="d-inline"
                            onsubmit="return confirm('¿Deseas desactivar este municipio?')"
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
                    <td colspan="5" class="text-center text-muted py-4">
                        No hay municipios registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 pagination-wrapper">
        {{ $municipios->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection