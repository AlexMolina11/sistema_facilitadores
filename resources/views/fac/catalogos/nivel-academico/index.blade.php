@extends('layouts.app')

@section('title', 'Niveles académicos | Facilitadores FEPADE')
@section('page-title', 'Catálogo de niveles académicos')
@section('page-subtitle', 'Administración de niveles académicos para la trayectoria educativa')

@section('content')

<x-ui.page-header title="Niveles académicos" subtitle="Listado de niveles académicos registrados en el sistema.">
    <a href="{{ route('fac.catalogos.nivel-academico.create') }}" class="btn btn-fepade">
        <i class="fa-solid fa-plus me-1"></i>
        Nuevo nivel académico
    </a>
</x-ui.page-header>

<x-ui.table-card
    title="Listado de niveles académicos"
    subtitle="Consulta, filtra y administra los niveles académicos disponibles."
    :items="$niveles"
    emptyTitle="No hay niveles académicos registrados."
    emptyMessage="Cuando registres niveles académicos, aparecerán en este listado."
>
    <x-slot name="filters">
        <form method="GET" action="{{ route('fac.catalogos.nivel-academico.index') }}" class="row g-3 align-items-end">
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
                <a href="{{ route('fac.catalogos.nivel-academico.index') }}" class="btn btn-outline-secondary w-100">
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
                            :editUrl="route('fac.catalogos.nivel-academico.edit', $nivel)"
                            :deleteUrl="route('fac.catalogos.nivel-academico.destroy', $nivel)"
                            deleteMessage="¿Deseas eliminar este nivel académico?"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection