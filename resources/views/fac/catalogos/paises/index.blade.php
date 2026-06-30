@extends('layouts.app')

@section('title', 'Países | Facilitadores FEPADE')
@section('page-title', 'Catálogo de países')
@section('page-subtitle', 'Administración de países disponibles en el sistema')

@section('content')

<x-ui.page-header title="Países" subtitle="Listado de países registrados en el sistema.">
    <a href="{{ route('fac.catalogos.paises.create') }}" class="btn btn-fepade">
        <i class="fa-solid fa-plus me-1"></i>
        Nuevo país
    </a>
</x-ui.page-header>

<x-ui.table-card
    title="Listado de países"
    subtitle="Consulta, filtra y administra los países disponibles."
    :items="$paises"
    emptyTitle="No hay países registrados."
    emptyMessage="Cuando registres países, aparecerán en este listado."
>
    <x-slot name="filters">
        <form method="GET" action="{{ route('fac.catalogos.paises.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Buscar</label>
                <input
                    type="text"
                    name="buscar"
                    value="{{ $buscar }}"
                    class="form-control"
                    placeholder="Buscar por nombre o código"
                >
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-navy w-100">
                    <i class="fa-solid fa-magnifying-glass me-1"></i>
                    Buscar
                </button>
            </div>

            <div class="col-md-3">
                <a href="{{ route('fac.catalogos.paises.index') }}" class="btn btn-outline-secondary w-100">
                    Limpiar
                </a>
            </div>
        </form>
    </x-slot>

    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Código</th>
                <th>Código MH</th>
                <th>Código MH Nuevo</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @foreach($paises as $pais)
                <tr>
                    <td class="fw-semibold">{{ $pais->nombre_pais }}</td>
                    <td>{{ $pais->codigo_pais }}</td>
                    <td>{{ $pais->mh_codigo_pais ?? '—' }}</td>
                    <td>{{ $pais->mh_codigo_pais_new ?? '—' }}</td>
                    <td>
                        @if($pais->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <x-ui.action-buttons
                            :editUrl="route('fac.catalogos.paises.edit', $pais->id_pais)"
                            :deleteUrl="route('fac.catalogos.paises.destroy', $pais->id_pais)"
                            deleteMessage="¿Deseas desactivar este país?"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection