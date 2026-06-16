@extends('layouts.app')

@section('title', 'Tipos de red social | Facilitadores FEPADE')
@section('page-title', 'Catálogo de tipos de red social')
@section('page-subtitle', 'Administración de tipos de red social disponibles para consultores')

@section('content')

<x-ui.page-header
    title="Tipos de red social"
    subtitle="Listado de tipos de red social registrados en el sistema."
>
    <a href="{{ route('fac.catalogos.tipo-red-social.create') }}"
       class="btn btn-fepade">
        Nuevo tipo de red social
    </a>
</x-ui.page-header>

<div class="fepade-card mb-4">

    <form method="GET"
          action="{{ route('fac.catalogos.tipo-red-social.index') }}"
          class="row g-3 align-items-end">

        <div class="col-md-6">
            <label class="form-label">Buscar</label>

            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                class="form-control"
                placeholder="Buscar por nombre o ícono"
            >
        </div>

        <div class="col-md-3">
            <button type="submit"
                    class="btn btn-navy w-100">
                Buscar
            </button>
        </div>

        <div class="col-md-3">
            <a href="{{ route('fac.catalogos.tipo-red-social.index') }}"
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
                    <th>Ícono</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>

                @forelse($tiposRedSocial as $tipoRedSocial)

                    <tr>

                        <td class="fw-semibold">
                            {{ $tipoRedSocial->nombre }}
                        </td>

                        <td>
                            @if($tipoRedSocial->icono)
                                @php
                                    $icono = $tipoRedSocial->icono;

                                    $iconosBootstrapAFontAwesome = [
                                        'bi bi-facebook' => 'fa-brands fa-facebook',
                                        'bi bi-linkedin' => 'fa-brands fa-linkedin',
                                    ];

                                    $iconoRender = $iconosBootstrapAFontAwesome[$icono] ?? $icono;
                                @endphp

                                <i class="{{ $iconoRender }}"></i>
                                <span class="ms-1">
                                    {{ $tipoRedSocial->icono }}
                                </span>
                            @else
                                —
                            @endif
                        </td>

                        <td>
                            @if($tipoRedSocial->activo)
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

                            <a href="{{ route('fac.catalogos.tipo-red-social.edit', $tipoRedSocial) }}"
                               class="btn btn-sm btn-outline-secondary">
                                Editar
                            </a>

                            <form
                                action="{{ route('fac.catalogos.tipo-red-social.destroy', $tipoRedSocial) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('¿Deseas desactivar este tipo de red social?')"
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
                            No hay tipos de red social registrados.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-4 pagination-wrapper">
        {{ $tiposRedSocial->links('pagination::bootstrap-5') }}
    </div>

</div>

@endsection