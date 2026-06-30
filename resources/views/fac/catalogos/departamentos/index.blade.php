@extends('layouts.app')

@section('title', 'Departamentos | Facilitadores FEPADE')
@section('page-title', 'Catálogo de departamentos')
@section('page-subtitle', 'Administración de departamentos disponibles en el sistema')

@section('content')

<x-ui.page-header title="Departamentos" subtitle="Listado de departamentos registrados en el sistema.">
    <a href="{{ route('fac.catalogos.departamentos.create') }}" class="btn btn-fepade">
        <i class="fa-solid fa-plus me-1"></i>
        Nuevo departamento
    </a>
</x-ui.page-header>

<x-ui.table-card
    title="Listado de departamentos"
    subtitle="Consulta, filtra y administra los departamentos disponibles."
    :items="$departamentos"
    emptyTitle="No hay departamentos registrados."
    emptyMessage="Cuando registres departamentos, aparecerán en este listado."
>
    <x-slot name="filters">
        <form method="GET" action="{{ route('fac.catalogos.departamentos.index') }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Buscar</label>
                <input
                    type="text"
                    name="buscar"
                    value="{{ $buscar }}"
                    class="form-control"
                    placeholder="Buscar por nombre, código MH o país"
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">País</label>
                <select name="id_pais" class="form-select">
                    <option value="">Todos</option>
                    @foreach($paises as $pais)
                        <option value="{{ $pais->id_pais }}" @selected(($idPais ?? '') == $pais->id_pais)>
                            {{ $pais->nombre_pais }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-navy w-100">
                    <i class="fa-solid fa-magnifying-glass me-1"></i>
                    Buscar
                </button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('fac.catalogos.departamentos.index') }}" class="btn btn-outline-secondary w-100">
                    Limpiar
                </a>
            </div>
        </form>
    </x-slot>

    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>País</th>
                <th>Nombre</th>
                <th>Código MH</th>
                <th>Georeferencia</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departamentos as $depto)
                <tr>
                    <td>{{ $depto->pais->nombre_pais ?? '—' }}</td>
                    <td class="fw-semibold">{{ $depto->nombre_departamento }}</td>
                    <td>{{ $depto->mh_codigo_depto ?? '—' }}</td>
                    <td>{{ $depto->georeferencia ?? '—' }}</td>
                    <td>
                        @if($depto->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <x-ui.action-buttons
                            :editUrl="route('fac.catalogos.departamentos.edit', $depto->id_departamento)"
                            :deleteUrl="route('fac.catalogos.departamentos.destroy', $depto->id_departamento)"
                            deleteMessage="¿Deseas desactivar este departamento?"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection