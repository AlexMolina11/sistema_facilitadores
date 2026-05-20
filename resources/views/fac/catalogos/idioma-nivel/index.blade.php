@extends('layouts.app')

@section('title', 'Nivel de idioma | Facilitadores FEPADE')
@section('page-title', 'Catálogo de niveles de idioma')
@section('page-subtitle', 'Administración de niveles de idioma')

@section('content')

<x-ui.page-header
    title="Niveles de idioma"
    subtitle="Listado de niveles de idioma registrados."
>
    <a href="{{ route('fac.catalogos.idioma-nivel.create') }}" class="btn btn-fepade">
        Nuevo nivel
    </a>
</x-ui.page-header>

<div class="fepade-card mb-4">
    <form method="GET" action="{{ route('fac.catalogos.idioma-nivel.index') }}" class="row g-3 align-items-end">

        <div class="col-md-6">
            <label class="form-label">Buscar</label>

            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                class="form-control"
                placeholder="Buscar por nombre"
            >
        </div>

        <div class="col-md-3">
            <button type="submit" class="btn btn-navy w-100">
                Buscar
            </button>
        </div>

        <div class="col-md-3">
            <a href="{{ route('fac.catalogos.idioma-nivel.index') }}" class="btn btn-outline-secondary w-100">
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

                @forelse($niveles as $nivel)

                    <tr>

                        <td class="fw-semibold">
                            {{ $nivel->nombre }}
                        </td>

                        <td>
                            @if($nivel->activo)
                                <span class="badge badge-success-soft">Activo</span>
                            @else
                                <span class="badge badge-warning-soft">Inactivo</span>
                            @endif
                        </td>

                        <td class="text-end">

                            <a
                                href="{{ route('fac.catalogos.idioma-nivel.edit', $nivel) }}"
                                class="btn btn-sm btn-outline-secondary"
                            >
                                Editar
                            </a>

                            <form
                                action="{{ route('fac.catalogos.idioma-nivel.destroy', $nivel) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('¿Deseas eliminar este nivel?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    Eliminar
                                </button>
                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            No hay niveles registrados.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-4 pagination-wrapper">
        {{ $niveles->links('pagination::bootstrap-5') }}
    </div>

</div>

@endsection