@extends('layouts.app')

@section('title', 'Idiomas | Facilitadores FEPADE')
@section('page-title', 'Catálogo de idiomas')
@section('page-subtitle', 'Administración de idiomas disponibles para los consultores')

@section('content')

<x-ui.page-header title="Idiomas" subtitle="Listado de idiomas registrados en el sistema.">
    <a href="{{ route('fac.catalogos.idiomas.create') }}" class="btn btn-fepade">
        Nuevo idioma
    </a>
</x-ui.page-header>

<div class="fepade-card mb-4">
    <form method="GET" action="{{ route('fac.catalogos.idiomas.index') }}" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Buscar</label>
            <input 
                type="text" 
                name="buscar" 
                value="{{ $buscar }}" 
                class="form-control" 
                placeholder="Buscar por nombre"
            >
        </div>

        <div class="col-md-3">
            <button type="submit" class="btn btn-navy w-100">
                Buscar
            </button>
        </div>

        <div class="col-md-3">
            <a href="{{ route('fac.catalogos.idiomas.index') }}" class="btn btn-outline-secondary w-100">
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
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($idiomas as $idioma)
                    <tr>
                        <td class="fw-semibold">
                            {{ $idioma->nombre }}
                        </td>

                        <td>
                            @if($idioma->activo)
                                <span class="badge badge-success-soft">Activo</span>
                            @else
                                <span class="badge badge-warning-soft">Inactivo</span>
                            @endif
                        </td>

                        <td class="text-end">
                            <a href="{{ route('fac.catalogos.idiomas.edit', $idioma) }}" class="btn btn-sm btn-outline-secondary">
                                Editar
                            </a>

                            <form 
                                action="{{ route('fac.catalogos.idiomas.destroy', $idioma) }}" 
                                method="POST" 
                                class="d-inline"
                                onsubmit="return confirm('¿Deseas eliminar este idioma?')"
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
                        <td colspan="3" class="text-center text-muted py-4">
                            No hay idiomas registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 pagination-wrapper">
        {{ $idiomas->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection