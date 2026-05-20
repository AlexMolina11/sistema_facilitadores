@extends('layouts.app')

@section('title', 'Tipos de formación | Facilitadores FEPADE')
@section('page-title', 'Catálogo de tipos de formación')
@section('page-subtitle', 'Administración de tipos de formación disponibles para consultores')

@section('content')

<x-ui.page-header
    title="Tipos de formación"
    subtitle="Listado de tipos de formación registrados en el sistema."
>
    <a href="{{ route('fac.catalogos.tipo-formacion.create') }}"
       class="btn btn-fepade">
        Nuevo tipo de formación
    </a>
</x-ui.page-header>

<div class="fepade-card mb-4">

    <form method="GET"
          action="{{ route('fac.catalogos.tipo-formacion.index') }}"
          class="row g-3 align-items-end">

        <div class="col-md-6">

            <label class="form-label">
                Buscar
            </label>

            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                class="form-control"
                placeholder="Buscar por nombre"
            >

        </div>

        <div class="col-md-3">

            <button type="submit"
                    class="btn btn-navy w-100">
                Buscar
            </button>

        </div>

        <div class="col-md-3">

            <a href="{{ route('fac.catalogos.tipo-formacion.index') }}"
               class="btn btn-outline-secondary w-100">
                Limpiar
            </a>

        </div>

    </form>

</div>

<div class="fepade-card">

    <div class="table-responsive">

        <table class="table align-middle">

            <thead>

                <tr>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>

            </thead>

            <tbody>

                @forelse($tiposFormacion as $tipoFormacion)

                    <tr>

                        <td class="fw-semibold">
                            {{ $tipoFormacion->nombre }}
                        </td>

                        <td>

                            @if($tipoFormacion->activo)

                                <span class="badge badge-success-soft">
                                    Activo
                                </span>

                            @else

                                <span class="badge badge-warning-soft">
                                    Inactivo
                                </span>

                            @endif

                        </td>

                        <td class="text-end">

                            <a href="{{ route('fac.catalogos.tipo-formacion.edit', $tipoFormacion) }}"
                               class="btn btn-sm btn-outline-secondary">
                                Editar
                            </a>

                            <form
                                action="{{ route('fac.catalogos.tipo-formacion.destroy', $tipoFormacion) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('¿Deseas desactivar este tipo de formación?')"
                            >

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger">
                                    Eliminar
                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="3"
                            class="text-center text-muted py-4">

                            No hay tipos de formación registrados.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-4 pagination-wrapper">

        {{ $tiposFormacion->links('pagination::bootstrap-5') }}

    </div>

</div>

@endsection