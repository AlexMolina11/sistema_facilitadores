@extends('layouts.app')

@section('title', 'Niveles de idioma | Facilitadores FEPADE')
@section('page-title', 'Catálogo de niveles de idioma')
@section('page-subtitle', 'Administración de niveles de dominio de idioma')

@section('content')

<x-ui.page-header title="Niveles de idioma" subtitle="Listado de niveles de idioma registrados.">
    <a href="{{ route('fac.catalogos.idioma-nivel.create') }}" class="btn btn-fepade">
        <i class="fa-solid fa-plus me-1"></i>
        Nuevo nivel
    </a>
</x-ui.page-header>

<x-ui.table-card
    title="Listado de niveles de idioma"
    subtitle="Consulta, filtra y administra los niveles disponibles."
    :items="$niveles"
    emptyTitle="No hay niveles de idioma registrados."
    emptyMessage="Cuando registres niveles de idioma, aparecerán en este listado."
>
    <x-slot name="filters">
        <form method="GET" action="{{ route('fac.catalogos.idioma-nivel.index') }}" class="row g-3 align-items-end">
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
                <a href="{{ route('fac.catalogos.idioma-nivel.index') }}" class="btn btn-outline-secondary w-100">
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
            @foreach($niveles as $nivel)
                <tr>
                    <td class="fw-semibold">{{ $nivel->nombre }}</td>
                    <td>
                        @if($nivel->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <x-ui.action-buttons
                            :editUrl="route('fac.catalogos.idioma-nivel.edit', $nivel)"
                            :deleteUrl="route('fac.catalogos.idioma-nivel.destroy', $nivel)"
                            deleteMessage="¿Deseas eliminar este nivel de idioma?"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection