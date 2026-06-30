@extends('layouts.app')

@section('title', 'Tipos de red social | Facilitadores FEPADE')
@section('page-title', 'Catálogo de tipos de red social')
@section('page-subtitle', 'Administración de redes sociales disponibles para el perfil del consultor')

@section('content')

<x-ui.page-header title="Tipos de red social" subtitle="Listado de tipos de red social registrados en el sistema.">
    <a href="{{ route('fac.catalogos.tipo-red-social.create') }}" class="btn btn-fepade">
        <i class="fa-solid fa-plus me-1"></i>
        Nuevo tipo
    </a>
</x-ui.page-header>

<x-ui.table-card
    title="Listado de tipos de red social"
    subtitle="Consulta, filtra y administra los tipos de red social."
    :items="$tiposRedSocial"
    emptyTitle="No hay tipos de red social registrados."
    emptyMessage="Cuando registres tipos de red social, aparecerán en este listado."
>
    <x-slot name="filters">
        <form method="GET" action="{{ route('fac.catalogos.tipo-red-social.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Buscar</label>
                <input type="text" name="buscar" value="{{ $buscar }}" class="form-control" placeholder="Buscar por nombre o icono">
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-navy w-100">
                    <i class="fa-solid fa-magnifying-glass me-1"></i>
                    Buscar
                </button>
            </div>

            <div class="col-md-3">
                <a href="{{ route('fac.catalogos.tipo-red-social.index') }}" class="btn btn-outline-secondary w-100">
                    Limpiar
                </a>
            </div>
        </form>
    </x-slot>

    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Icono</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @foreach($tiposRedSocial as $tipoRedSocial)
                <tr>
                    <td class="fw-semibold">{{ $tipoRedSocial->nombre }}</td>
                    <td>
                        @if($tipoRedSocial->icono)
                            <code class="fepade-code">{{ $tipoRedSocial->icono }}</code>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if($tipoRedSocial->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <x-ui.action-buttons
                            :editUrl="route('fac.catalogos.tipo-red-social.edit', $tipoRedSocial)"
                            :deleteUrl="route('fac.catalogos.tipo-red-social.destroy', $tipoRedSocial)"
                            deleteMessage="¿Deseas eliminar este tipo de red social?"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection