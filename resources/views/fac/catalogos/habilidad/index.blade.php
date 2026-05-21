@extends('layouts.app')

@section('title', 'Habilidades | Facilitadores FEPADE')
@section('page-title', 'Catálogo de habilidades')
@section('page-subtitle', 'Administración de habilidades disponibles para consultores')

@section('content')

<x-ui.page-header
    title="Habilidades"
    subtitle="Listado de habilidades registradas en el sistema."
>
    <a href="{{ route('fac.catalogos.habilidad.create') }}"
       class="btn btn-fepade">
        Nueva habilidad
    </a>
</x-ui.page-header>

<div class="fepade-card mb-4">

    <form method="GET"
          action="{{ route('fac.catalogos.habilidad.index') }}"
          class="row g-3 align-items-end">

        <div class="col-md-6">
            <label class="form-label">Buscar</label>

            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                class="form-control"
                placeholder="Buscar por habilidad o tipo"
            >
        </div>

        <div class="col-md-3">
            <button type="submit"
                    class="btn btn-navy w-100">
                Buscar
            </button>
        </div>

        <div class="col-md-3">
            <a href="{{ route('fac.catalogos.habilidad.index') }}"
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
                    <th>Tipo de habilidad</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>

                @forelse($habilidades as $habilidad)

                    <tr>

                        <td>
                            {{ $habilidad->tipoHabilidad->nombre ?? '—' }}
                        </td>

                        <td class="fw-semibold">
                            {{ $habilidad->nombre }}
                        </td>

                        <td>
                            @if($habilidad->activo)
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

                            <a href="{{ route('fac.catalogos.habilidad.edit', $habilidad) }}"
                               class="btn btn-sm btn-outline-secondary">
                                Editar
                            </a>

                            <form
                                action="{{ route('fac.catalogos.habilidad.destroy', $habilidad) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('¿Deseas desactivar esta habilidad?')"
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
                            No hay habilidades registradas.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-4 pagination-wrapper">
        {{ $habilidades->links('pagination::bootstrap-5') }}
    </div>

</div>

@endsection