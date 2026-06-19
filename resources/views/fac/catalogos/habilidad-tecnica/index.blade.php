@extends('layouts.app')

@section('title', 'Habilidades técnicas | Facilitadores FEPADE')
@section('page-title', 'Habilidades técnicas')
@section('page-subtitle', 'Administración de habilidades técnicas vinculadas a áreas de especialización')

@section('content')
<x-ui.page-header title="Habilidades técnicas" subtitle="Cada habilidad técnica pertenece a un área de especialización.">
    <a href="{{ route('fac.catalogos.habilidad-tecnica.create') }}" class="btn btn-fepade">Nueva habilidad técnica</a>
</x-ui.page-header>

<form method="GET" class="card-fepade mb-3">
    <div class="input-group">
        <input type="text" name="buscar" class="form-control" value="{{ $buscar }}" placeholder="Buscar por habilidad o área">
        <button class="btn btn-outline-secondary">Buscar</button>
    </div>
</form>

<div class="card-fepade">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Habilidad técnica</th><th>Área</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
            <tbody>
                @forelse($habilidadesTecnicas as $habilidad)
                    <tr>
                        <td>{{ $habilidad->nombre }}</td>
                        <td>{{ $habilidad->areaEspecializacion?->nombre ?? '—' }}</td>
                        <td><span class="badge {{ $habilidad->activo ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $habilidad->activo ? 'Activo' : 'Inactivo' }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('fac.catalogos.habilidad-tecnica.edit', $habilidad) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                            <form action="{{ route('fac.catalogos.habilidad-tecnica.destroy', $habilidad) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta habilidad técnica?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No hay habilidades técnicas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $habilidadesTecnicas->links() }}</div>
</div>
@endsection
