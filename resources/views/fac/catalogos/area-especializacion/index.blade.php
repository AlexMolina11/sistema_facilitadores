@extends('layouts.app')

@section('title', 'Áreas de especialización | Facilitadores FEPADE')
@section('page-title', 'Áreas de especialización')
@section('page-subtitle', 'Administración de áreas que podrán seleccionar los consultores')

@section('content')
<x-ui.page-header title="Áreas de especialización" subtitle="Catálogo de áreas temáticas del perfil profesional.">
    <a href="{{ route('fac.catalogos.area-especializacion.create') }}" class="btn btn-fepade">Nueva área</a>
</x-ui.page-header>

<form method="GET" class="card-fepade mb-3">
    <div class="input-group">
        <input type="text" name="buscar" class="form-control" value="{{ $buscar }}" placeholder="Buscar por nombre">
        <button class="btn btn-outline-secondary">Buscar</button>
    </div>
</form>

<div class="card-fepade">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Nombre</th><th>Descripción</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
                @forelse($areas as $area)
                    <tr>
                        <td>{{ $area->nombre }}</td>
                        <td>{{ $area->descripcion ?? '—' }}</td>
                        <td><span class="badge {{ $area->activo ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $area->activo ? 'Activo' : 'Inactivo' }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('fac.catalogos.area-especializacion.edit', $area) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                            <form action="{{ route('fac.catalogos.area-especializacion.destroy', $area) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta área?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No hay áreas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $areas->links() }}</div>
</div>
@endsection
