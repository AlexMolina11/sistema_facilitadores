@extends('layouts.app')

@section('title', 'Distritos | Facilitadores FEPADE')
@section('page-title', 'Catálogo de Distritos')
@section('page-subtitle', 'Administración de Distritos disponibles en el sistema')

@section('content')
<x-ui.page-header title="Distritos" subtitle="Listado de Distritos registrados en el sistema.">
    <a href="{{ route('fac.catalogos.municipios.create') }}" class="btn btn-fepade">
        Nuevo municipio
    </a>
</x-ui.page-header>

<div class="fepade-card mb-4">
    <form method="GET" action="{{ route('fac.catalogos.municipios.index') }}" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Buscar</label>
            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                class="form-control"
                placeholder="Buscar por nombre, código MH, departamento o país"
            >
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-navy w-100">Buscar</button>
        </div>
        <div class="col-md-3">
            <a href="{{ route('fac.catalogos.municipios.index') }}" class="btn btn-outline-secondary w-100">
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
                    <th>Departamento</th>
                    <th>Municipio</th>
                    <th>Distrito</th>
                    <th>Código MH</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($municipios as $municipio)
                <tr>
                    <td>{{ $municipio->pais->nombre_pais ?? '—' }}</td>
                    <td>{{ $municipio->departamento->nombre_departamento ?? '—' }}</td>
                    <td>{{ $municipio->municipioMh->municipio_mh_nombre ?? '—' }}</td>
                    <td class="fw-semibold">{{ $municipio->nombre_distrito }}</td>
                    <td>{{ $municipio->mh_codigo_distrito ?? '—' }}</td>
                    <td>
                        @if($municipio->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('fac.catalogos.municipios.edit', $municipio->id_municipio) }}"
                           class="btn btn-sm btn-outline-secondary">
                            Editar
                        </a>
                        <form
                            action="{{ route('fac.catalogos.municipios.destroy', $municipio->id_municipio) }}"
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
                    <td colspan="7" class="text-center text-muted py-4">
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