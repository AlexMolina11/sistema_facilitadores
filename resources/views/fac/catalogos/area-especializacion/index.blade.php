@extends('layouts.app')

@section('title', 'Áreas de especialización | Facilitadores FEPADE')
@section('page-title', 'Áreas de especialización')
@section('page-subtitle', 'Administración de áreas que podrán seleccionar los consultores')

@section('content')

<x-ui.page-header title="Áreas de especialización" subtitle="Listado de áreas temáticas disponibles para el perfil profesional.">
    <a href="{{ route('fac.catalogos.area-especializacion.create') }}" class="btn btn-fepade">
        Nueva área
    </a>
</x-ui.page-header>

<div class="fepade-card mb-4">
    <form method="GET" action="{{ route('fac.catalogos.area-especializacion.index') }}" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Buscar</label>
            <input type="text" name="buscar" value="{{ $buscar }}" class="form-control" placeholder="Buscar por nombre o descripción">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-navy w-100">Buscar</button>
        </div>
        <div class="col-md-3">
            <a href="{{ route('fac.catalogos.area-especializacion.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
        </div>
    </form>
</div>

<div class="fepade-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($areas as $area)
                    <tr>
                        <td class="fw-semibold">{{ $area->nombre }}</td>
                        <td>{{ $area->descripcion ?: '—' }}</td>
                        <td>
                            @if($area->activo)
                                <span class="badge badge-success-soft">Activo</span>
                            @else
                                <span class="badge badge-warning-soft">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('fac.catalogos.area-especializacion.edit', $area) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            <form action="{{ route('fac.catalogos.area-especializacion.destroy', $area) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar esta área de especialización?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No hay áreas de especialización registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 pagination-wrapper">
        {{ $areas->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
