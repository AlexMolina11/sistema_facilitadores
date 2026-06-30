@extends('layouts.app')

@section('title', 'Tipos de teléfono | Facilitadores FEPADE')
@section('page-title', 'Catálogo de tipos de teléfono')
@section('page-subtitle', 'Administración de tipos de teléfono disponibles en el sistema')

@section('content')

<x-ui.page-header title="Tipos de teléfono" subtitle="Listado de tipos de teléfono registrados en el sistema.">
    <a href="{{ route('fac.catalogos.tipo_telefono.create') }}" class="btn btn-fepade">
        <i class="fa-solid fa-plus me-1"></i>
        Nuevo tipo
    </a>
</x-ui.page-header>

<x-ui.table-card
    title="Listado de tipos de teléfono"
    subtitle="Consulta, filtra y administra los tipos de teléfono."
    :items="$tipos"
    emptyTitle="No hay tipos de teléfono registrados."
    emptyMessage="Cuando registres tipos de teléfono, aparecerán en este listado."
>
    <x-slot name="filters">
        <form method="GET" action="{{ route('fac.catalogos.tipo_telefono.index') }}" class="row g-3 align-items-end">
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
                <a href="{{ route('fac.catalogos.tipo_telefono.index') }}" class="btn btn-outline-secondary w-100">
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
            @foreach($tipos as $tipo)
                <tr>
                    <td class="fw-semibold">{{ $tipo->nombre }}</td>
                    <td>
                        @if($tipo->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <x-ui.action-buttons
                            :editUrl="route('fac.catalogos.tipo_telefono.edit', $tipo->id_tipo_telefono)"
                            :deleteUrl="route('fac.catalogos.tipo_telefono.destroy', $tipo->id_tipo_telefono)"
                            deleteMessage="¿Deseas eliminar este tipo de teléfono?"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection