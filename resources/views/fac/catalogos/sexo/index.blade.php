@extends('layouts.app')

@section('title', 'Sexo | Facilitadores FEPADE')
@section('page-title', 'Catálogo de sexo')
@section('page-subtitle', 'Administración de sexos')

@section('content')

<x-ui.page-header
    title="Sexos"
    subtitle="Listado de sexos registrados."
>
    <a href="{{ route('fac.catalogos.sexo.create') }}"
       class="btn btn-fepade">
        Nuevo sexo
    </a>
</x-ui.page-header>

<div class="fepade-card mb-4">

    <form method="GET"
          action="{{ route('fac.catalogos.sexo.index') }}"
          class="row g-3 align-items-end">

        <div class="col-md-6">
            <label class="form-label">Buscar</label>

            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                class="form-control"
                placeholder="Buscar por nombre">
        </div>

        <div class="col-md-3">
            <button type="submit" class="btn btn-navy w-100">
                Buscar
            </button>
        </div>

        <div class="col-md-3">
            <a href="{{ route('fac.catalogos.sexo.index') }}"
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

                @forelse($sexos as $sexo)

                    <tr>

                        <td>{{ $sexo->nombre }}</td>

                        <td>
                            @if($sexo->activo)
                                <span class="badge badge-success-soft">Activo</span>
                            @else
                                <span class="badge badge-warning-soft">Inactivo</span>
                            @endif
                        </td>

                        <td class="text-end">

                            <a href="{{ route('fac.catalogos.sexo.edit', $sexo) }}"
                               class="btn btn-sm btn-outline-secondary">
                                Editar
                            </a>

                            <form
                                action="{{ route('fac.catalogos.sexo.destroy', $sexo) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('¿Deseas eliminar este sexo?')">

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
                        <td colspan="3" class="text-center text-muted py-4">
                            No hay registros.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-4 pagination-wrapper">
        {{ $sexos->links('pagination::bootstrap-5') }}
    </div>

</div>

@endsection