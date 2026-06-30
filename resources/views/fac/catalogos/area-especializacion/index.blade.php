@extends('layouts.app')

@section('title', 'Áreas de especialización | Facilitadores FEPADE')
@section('page-title', 'Áreas de especialización')
@section('page-subtitle', 'Administración de áreas que podrán seleccionar los consultores')

@section('content')

<x-ui.page-header 
    title="Áreas de especialización" 
    subtitle="Listado de áreas temáticas disponibles para el perfil profesional."
>
    <a href="{{ route('fac.catalogos.area-especializacion.create') }}" class="btn btn-fepade">
        <i class="fa-solid fa-plus me-1"></i>
        Nueva área
    </a>
</x-ui.page-header>

<x-ui.table-card
    title="Listado de áreas"
    subtitle="Consulta, filtra y administra las áreas de especialización disponibles."
    :items="$areas"
    emptyTitle="No hay áreas de especialización registradas."
    emptyMessage="Cuando registres áreas, aparecerán en este listado."
>
    <x-slot name="filters">
        <form method="GET" action="{{ route('fac.catalogos.area-especializacion.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Buscar</label>
                <input 
                    type="text" 
                    name="buscar" 
                    value="{{ $buscar }}" 
                    class="form-control" 
                    placeholder="Buscar por nombre o descripción"
                >
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-fepade w-100">
                    <i class="fa-solid fa-magnifying-glass me-1"></i>
                    Buscar
                </button>
            </div>

            <div class="col-md-3">
                <a href="{{ route('fac.catalogos.area-especializacion.index') }}" class="btn btn-outline-secondary w-100">
                    Limpiar
                </a>
            </div>
        </form>
    </x-slot>

    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($areas as $area)
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
                        <x-ui.action-buttons
                            :editUrl="route('fac.catalogos.area-especializacion.edit', $area)"
                            :deleteUrl="route('fac.catalogos.area-especializacion.destroy', $area)"
                            deleteMessage="¿Deseas eliminar esta área de especialización?"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection