@extends('layouts.app')

@section('title', 'Habilidades técnicas | Facilitadores FEPADE')
@section('page-title', 'Habilidades técnicas')
@section('page-subtitle', 'Administración de habilidades técnicas vinculadas a áreas de especialización')

@section('content')

<x-ui.page-header 
    title="Habilidades técnicas" 
    subtitle="Listado de habilidades técnicas clasificadas por área de especialización."
>
    <a href="{{ route('fac.catalogos.habilidad-tecnica.create') }}" class="btn btn-fepade">
        <i class="fa-solid fa-plus me-1"></i>
        Nueva habilidad técnica
    </a>
</x-ui.page-header>

<x-ui.table-card
    title="Listado de habilidades"
    subtitle="Consulta, filtra y administra las habilidades técnicas disponibles."
    :items="$habilidadesTecnicas"
    emptyTitle="No hay habilidades técnicas registradas."
    emptyMessage="Cuando registres habilidades, aparecerán en este listado."
>
    <x-slot name="filters">
        <form method="GET" action="{{ route('fac.catalogos.habilidad-tecnica.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Buscar</label>
                <input 
                    type="text" 
                    name="buscar" 
                    value="{{ $buscar }}" 
                    class="form-control" 
                    placeholder="Buscar por habilidad o área"
                >
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-navy w-100">
                    <i class="fa-solid fa-magnifying-glass me-1"></i>
                    Buscar
                </button>
            </div>

            <div class="col-md-3">
                <a href="{{ route('fac.catalogos.habilidad-tecnica.index') }}" class="btn btn-outline-secondary w-100">
                    Limpiar
                </a>
            </div>
        </form>
    </x-slot>

    <table class="table table-hover align-middle">
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
            @foreach($habilidadesTecnicas as $habilidad)
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
                    <td>
                        <x-ui.action-buttons
                            :editUrl="route('fac.catalogos.habilidad-tecnica.edit', $habilidad)"
                            :deleteUrl="route('fac.catalogos.habilidad-tecnica.destroy', $habilidad)"
                            deleteMessage="¿Deseas eliminar esta habilidad técnica?"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection