@extends('layouts.app')

@section('title', 'Idiomas | Facilitadores FEPADE')
@section('page-title', 'Catálogo de idiomas')
@section('page-subtitle', 'Administración de idiomas disponibles para los consultores')

@section('content')

<x-ui.page-header title="Idiomas" subtitle="Listado de idiomas registrados en el sistema.">
    <a href="{{ route('fac.catalogos.idiomas.create') }}" class="btn btn-fepade">
        <i class="fa-solid fa-plus me-1"></i>
        Nuevo idioma
    </a>
</x-ui.page-header>

<x-ui.table-card
    title="Listado de idiomas"
    subtitle="Consulta, filtra y administra los idiomas disponibles."
    :items="$idiomas"
    emptyTitle="No hay idiomas registrados."
    emptyMessage="Cuando registres idiomas, aparecerán en este listado."
>
    <x-slot name="filters">
        <form method="GET" action="{{ route('fac.catalogos.idiomas.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Buscar</label>
                <input type="text" name="buscar" value="{{ $buscar }}" class="form-control" placeholder="Buscar por nombre">
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-navy w-100">
                    <i class="fa-solid fa-magnifying-glass me-1"></i>
                    Buscar
                </button>
            </div>

            <div class="col-md-3">
                <a href="{{ route('fac.catalogos.idiomas.index') }}" class="btn btn-outline-secondary w-100">
                    Limpiar
                </a>
            </div>
        </form>
    </x-slot>

    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($idiomas as $idioma)
                <tr>
                    <td class="fw-semibold">{{ $idioma->nombre }}</td>
                    <td>
                        @if($idioma->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <x-ui.action-buttons
                            :editUrl="route('fac.catalogos.idiomas.edit', $idioma)"
                            :deleteUrl="route('fac.catalogos.idiomas.destroy', $idioma)"
                            deleteMessage="¿Deseas eliminar este idioma?"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection