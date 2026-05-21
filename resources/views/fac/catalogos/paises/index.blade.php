@extends('layouts.app')

@section('title', 'Países | Facilitadores FEPADE')
@section('page-title', 'Catálogo de países')
@section('page-subtitle', 'Administración de países disponibles en el sistema')

@section('content')
<x-ui.page-header title="Países" subtitle="Listado de países registrados en el sistema.">
    <a href="{{ route('fac.catalogos.paises.create') }}" class="btn btn-fepade">
        Nuevo país
    </a>
</x-ui.page-header>

<div class="fepade-card mb-4">
    <form method="GET" action="{{ route('fac.catalogos.paises.index') }}" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Buscar</label>
            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                class="form-control"
                placeholder="Buscar por nombre o código"
            >
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-navy w-100">Buscar</button>
        </div>
        <div class="col-md-3">
            <a href="{{ route('fac.catalogos.paises.index') }}" class="btn btn-outline-secondary w-100">
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
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Código MH</th>
                    <th>Código MH Nuevo</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paises as $pais)
                <tr>
                    <td class="fw-semibold">{{ $pais->nombre_pais }}</td>
                    <td>{{ $pais->codigo_pais }}</td>
                    <td>{{ $pais->mh_codigo_pais ?? '—' }}</td>
                    <td>{{ $pais->mh_codigo_pais_new ?? '—' }}</td>
                    <td>
                        @if($pais->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('fac.catalogos.paises.edit', $pais->id_pais) }}" class="btn btn-sm btn-outline-secondary">
                            Editar
                        </a>
                        <form
                            action="{{ route('fac.catalogos.paises.destroy', $pais->id_pais) }}"
                            method="POST"
                            class="d-inline"
                            onsubmit="return confirm('¿Deseas desactivar este país?')"
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
                        No hay países registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 pagination-wrapper">
        {{ $paises->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection