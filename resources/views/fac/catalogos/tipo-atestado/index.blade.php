@extends('layouts.app')

@section('title', 'Tipos de atestado | Facilitadores FEPADE')
@section('page-title', 'Catálogo de tipos de atestado')
@section('page-subtitle', 'Administración de tipos de atestado disponibles para consultores')

@section('content')

<x-ui.page-header title="Tipos de atestado" subtitle="Listado de tipos de atestado registrados en el sistema.">
    <a href="{{ route('fac.catalogos.tipo-atestado.create') }}" class="btn btn-fepade">
        <i class="fa-solid fa-plus me-1"></i>
        Nuevo tipo de atestado
    </a>
</x-ui.page-header>

<x-ui.table-card
    title="Listado de tipos de atestado"
    subtitle="Consulta, filtra y administra los tipos de atestado disponibles."
    :items="$tiposAtestado"
    emptyTitle="No hay tipos de atestado registrados."
    emptyMessage="Cuando registres tipos de atestado, aparecerán en este listado."
>
    <x-slot name="filters">
        <form method="GET" action="{{ route('fac.catalogos.tipo-atestado.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Buscar</label>
                <input type="text" name="buscar" value="{{ $buscar }}" class="form-control" placeholder="Buscar por nombre o tipo de formación">
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-navy w-100">
                    <i class="fa-solid fa-magnifying-glass me-1"></i>
                    Buscar
                </button>
            </div>

            <div class="col-md-3">
                <a href="{{ route('fac.catalogos.tipo-atestado.index') }}" class="btn btn-outline-secondary w-100">
                    Limpiar
                </a>
            </div>
        </form>
    </x-slot>

    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Tipo de formación</th>
                <th>Tipo de atestado</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @foreach($tiposAtestado as $tipoAtestado)
                <tr>
                    <td>{{ $tipoAtestado->tipoFormacion?->nombre ?? '—' }}</td>
                    <td class="fw-semibold">{{ $tipoAtestado->nombre }}</td>
                    <td>
                        @if($tipoAtestado->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <x-ui.action-buttons
                            :editUrl="route('fac.catalogos.tipo-atestado.edit', $tipoAtestado)"
                            :deleteUrl="route('fac.catalogos.tipo-atestado.destroy', $tipoAtestado)"
                            deleteMessage="¿Deseas eliminar este tipo de atestado?"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection