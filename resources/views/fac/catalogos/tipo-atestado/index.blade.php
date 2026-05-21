@extends('layouts.app')

@section('title', 'Tipos de atestado | Facilitadores FEPADE')
@section('page-title', 'Catálogo de tipos de atestado')
@section('page-subtitle', 'Administración de tipos de atestado disponibles para consultores')

@section('content')

<x-ui.page-header
    title="Tipos de atestado"
    subtitle="Listado de tipos de atestado registrados en el sistema."
>
    <a href="{{ route('fac.catalogos.tipo-atestado.create') }}"
       class="btn btn-fepade">
        Nuevo tipo de atestado
    </a>
</x-ui.page-header>

<div class="fepade-card mb-4">

    <form method="GET"
          action="{{ route('fac.catalogos.tipo-atestado.index') }}"
          class="row g-3 align-items-end">

        <div class="col-md-6">
            <label class="form-label">Buscar</label>

            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                class="form-control"
                placeholder="Buscar por nombre o tipo de formación"
            >
        </div>

        <div class="col-md-3">
            <button type="submit" class="btn btn-navy w-100">
                Buscar
            </button>
        </div>

        <div class="col-md-3">
            <a href="{{ route('fac.catalogos.tipo-atestado.index') }}"
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
                    <th>Tipo de formación</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>

                @forelse($tiposAtestado as $tipoAtestado)

                    <tr>
                        <td>
                            {{ $tipoAtestado->tipoFormacion->nombre ?? '—' }}
                        </td>

                        <td class="fw-semibold">
                            {{ $tipoAtestado->nombre }}
                        </td>

                        <td>
                            @if($tipoAtestado->activo)
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

                            <a href="{{ route('fac.catalogos.tipo-atestado.edit', $tipoAtestado) }}"
                               class="btn btn-sm btn-outline-secondary">
                                Editar
                            </a>

                            <form
                                action="{{ route('fac.catalogos.tipo-atestado.destroy', $tipoAtestado) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('¿Deseas desactivar este tipo de atestado?')"
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
                        <td colspan="4"
                            class="text-center text-muted py-4">
                            No hay tipos de atestado registrados.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-4 pagination-wrapper">
        {{ $tiposAtestado->links('pagination::bootstrap-5') }}
    </div>

</div>

@endsection