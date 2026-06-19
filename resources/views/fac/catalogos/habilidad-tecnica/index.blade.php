@extends('layouts.app')

@section('title', 'Habilidades técnicas | Facilitadores FEPADE')
@section('page-title', 'Habilidades técnicas')
@section('page-subtitle', 'Administración de habilidades técnicas vinculadas a áreas de especialización')

@section('content')

<x-ui.page-header title="Habilidades técnicas" subtitle="Listado de habilidades técnicas clasificadas por área de especialización.">
    <a href="{{ route('fac.catalogos.habilidad-tecnica.create') }}" class="btn btn-fepade">
        Nueva habilidad técnica
    </a>
</x-ui.page-header>

<div class="fepade-card mb-4">
    <form method="GET" action="{{ route('fac.catalogos.habilidad-tecnica.index') }}" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Buscar</label>
            <input type="text" name="buscar" value="{{ $buscar }}" class="form-control" placeholder="Buscar por habilidad o área">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-navy w-100">Buscar</button>
        </div>
        <div class="col-md-3">
            <a href="{{ route('fac.catalogos.habilidad-tecnica.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
        </div>
    </form>
</div>

<div class="fepade-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Habilidad técnica</th>
                    <th>Área de especialización</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($habilidadesTecnicas as $habilidad)
                    <tr>
                        <td class="fw-semibold">{{ $habilidad->nombre }}</td>
                        <td>{{ $habilidad->areaEspecializacion?->nombre ?? '—' }}</td>
                        <td>{{ $habilidad->descripcion ?: '—' }}</td>
                        <td>
                            @if($habilidad->activo)
                                <span class="badge badge-success-soft">Activo</span>
                            @else
                                <span class="badge badge-warning-soft">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('fac.catalogos.habilidad-tecnica.edit', $habilidad) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            <form action="{{ route('fac.catalogos.habilidad-tecnica.destroy', $habilidad) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar esta habilidad técnica?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No hay habilidades técnicas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 pagination-wrapper">
        {{ $habilidadesTecnicas->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
