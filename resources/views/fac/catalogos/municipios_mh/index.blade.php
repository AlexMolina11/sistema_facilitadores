@extends('layouts.app')

@section('title', 'Municipios MH | Facilitadores FEPADE')
@section('page-title', 'Catálogo de municipios MH')
@section('page-subtitle', 'Administración de municipios MH disponibles en el sistema')

@section('content')

<x-ui.page-header title="Municipios MH" subtitle="Listado de municipios MH registrados en el sistema.">
    <a href="{{ route('fac.catalogos.municipios_mh.create') }}" class="btn btn-fepade">
        <i class="fa-solid fa-plus me-1"></i>
        Nuevo municipio
    </a>
</x-ui.page-header>

<x-ui.table-card
    title="Listado de municipios MH"
    subtitle="Consulta, filtra y administra los municipios MH disponibles."
    :items="$municipios"
    emptyTitle="No hay municipios registrados."
    emptyMessage="Cuando registres municipios, aparecerán en este listado."
>
    <x-slot name="filters">
        <form method="GET" action="{{ route('fac.catalogos.municipios_mh.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Buscar</label>
                <input
                    type="text"
                    name="buscar"
                    value="{{ $buscar }}"
                    class="form-control"
                    placeholder="Buscar por nombre, código MH, país o departamento"
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">País</label>
                <select name="id_pais" id="filtroPais" class="form-select">
                    <option value="">Todos</option>
                    @foreach($paises as $pais)
                        <option value="{{ $pais->id_pais }}" @selected(($idPais ?? '') == $pais->id_pais)>
                            {{ $pais->nombre_pais }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Departamento</label>
                <select name="id_departamento" id="filtroDepartamento" class="form-select">
                    <option value="">Todos</option>
                    @foreach($departamentos as $departamento)
                        <option 
                            value="{{ $departamento->id_departamento }}"
                            data-id-pais="{{ $departamento->id_pais }}"
                            @selected(($idDepartamento ?? '') == $departamento->id_departamento)
                        >
                            {{ $departamento->nombre_departamento }}
                            @if($departamento->pais)
                                — {{ $departamento->pais->nombre_pais }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1">
                <button type="submit" class="btn btn-navy w-100">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>

            <div class="col-md-1">
                <a href="{{ route('fac.catalogos.municipios_mh.index') }}" class="btn btn-outline-secondary w-100">
                    Limpiar
                </a>
            </div>
        </form>
    </x-slot>

    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>País</th>
                <th>Departamento</th>
                <th>Nombre</th>
                <th>Código MH</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @foreach($municipios as $municipio)
                <tr>
                    <td>{{ $municipio->departamento?->pais?->nombre_pais ?? '—' }}</td>
                    <td>{{ $municipio->departamento?->nombre_departamento ?? '—' }}</td>
                    <td class="fw-semibold">{{ $municipio->municipio_mh_nombre }}</td>
                    <td>{{ $municipio->mh_codigo_municipio ?? '—' }}</td>
                    <td>
                        @if($municipio->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <x-ui.action-buttons
                            :editUrl="route('fac.catalogos.municipios_mh.edit', $municipio->id_municipio_mh)"
                            :deleteUrl="route('fac.catalogos.municipios_mh.destroy', $municipio->id_municipio_mh)"
                            deleteMessage="¿Deseas desactivar este municipio?"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const paisSelect = document.getElementById('filtroPais');
    const departamentoSelect = document.getElementById('filtroDepartamento');

    if (!paisSelect || !departamentoSelect) return;

    function filtrarDepartamentos() {
        const idPais = paisSelect.value;
        const selectedDepartamento = departamentoSelect.value;

        let departamentoSigueVisible = false;

        Array.from(departamentoSelect.options).forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            const perteneceAlPais = !idPais || option.dataset.idPais === idPais;
            option.hidden = !perteneceAlPais;

            if (option.value === selectedDepartamento && perteneceAlPais) {
                departamentoSigueVisible = true;
            }
        });

        if (!departamentoSigueVisible) {
            departamentoSelect.value = '';
        }
    }

    paisSelect.addEventListener('change', filtrarDepartamentos);

    filtrarDepartamentos();
});
</script>
@endpush