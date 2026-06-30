@extends('layouts.app')

@section('title', 'Sexo | Facilitadores FEPADE')
@section('page-title', 'Catálogo de sexo')
@section('page-subtitle', 'Administración del catálogo de sexo para el perfil del consultor')

@section('content')

<x-ui.page-header title="Sexo" subtitle="Listado de opciones registradas en el catálogo.">
    <a href="{{ route('fac.catalogos.sexo.create') }}" class="btn btn-fepade">
        <i class="fa-solid fa-plus me-1"></i>
        Nueva opción
    </a>
</x-ui.page-header>

<x-ui.table-card
    title="Listado de opciones"
    subtitle="Consulta, filtra y administra las opciones disponibles."
    :items="$sexos"
    emptyTitle="No hay opciones registradas."
    emptyMessage="Cuando registres opciones, aparecerán en este listado."
>
    <x-slot name="filters">
        <form method="GET" action="{{ route('fac.catalogos.sexo.index') }}" class="row g-3 align-items-end">
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
                <a href="{{ route('fac.catalogos.sexo.index') }}" class="btn btn-outline-secondary w-100">
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
            @foreach($sexos as $sexo)
                <tr>
                    <td class="fw-semibold">{{ $sexo->nombre }}</td>
                    <td>
                        @if($sexo->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <x-ui.action-buttons
                            :editUrl="route('fac.catalogos.sexo.edit', $sexo)"
                            :deleteUrl="route('fac.catalogos.sexo.destroy', $sexo)"
                            deleteMessage="¿Deseas eliminar esta opción?"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection