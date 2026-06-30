@extends('layouts.app')

@section('title', 'Distritos | Facilitadores FEPADE')
@section('page-title', 'Catálogo de distritos')
@section('page-subtitle', 'Administración de distritos disponibles en el sistema')

@section('content')

<x-ui.page-header title="Distritos" subtitle="Listado de distritos registrados en el sistema.">
    <a href="{{ route('fac.catalogos.municipios.create') }}" class="btn btn-fepade">
        <i class="fa-solid fa-plus me-1"></i>
        Nuevo distrito
    </a>
</x-ui.page-header>

<x-ui.table-card
    title="Listado de distritos"
    subtitle="Consulta, filtra y administra los distritos disponibles."
    :items="$municipios"
    emptyTitle="No hay distritos registrados."
    emptyMessage="Cuando registres distritos, aparecerán en este listado."
>
    <x-slot name="filters">
        <form method="GET" action="{{ route('fac.catalogos.municipios.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Buscar</label>
                <input
                    type="text"
                    name="buscar"
                    value="{{ $buscar }}"
                    class="form-control"
                    placeholder="Buscar por distrito, código, municipio, depto. o país"
                >
            </div>

            <div class="col-md-2">
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
                        <option value="{{ $departamento->id_departamento }}" @selected(($idDepartamento ?? '') == $departamento->id_departamento)>
                            {{ $departamento->nombre_departamento }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Municipio</label>
                <select name="id_municipio_mh" id="filtroMunicipioMh" class="form-select">
                    <option value="">Todos</option>
                    @foreach($municipiosMh as $municipioMh)
                        <option value="{{ $municipioMh->id_municipio_mh }}" @selected(($idMunicipioMh ?? '') == $municipioMh->id_municipio_mh)>
                            {{ $municipioMh->municipio_mh_nombre }}
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
                <a href="{{ route('fac.catalogos.municipios.index') }}" class="btn btn-outline-secondary w-100">
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
                <th>Municipio</th>
                <th>Distrito</th>
                <th>Código MH</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @foreach($municipios as $municipio)
                <tr>
                    <td>{{ $municipio->pais?->nombre_pais ?? '—' }}</td>
                    <td>{{ $municipio->departamento?->nombre_departamento ?? '—' }}</td>
                    <td>{{ $municipio->municipioMh?->municipio_mh_nombre ?? '—' }}</td>
                    <td class="fw-semibold">{{ $municipio->nombre_distrito }}</td>
                    <td>{{ $municipio->mh_codigo_distrito ?? '—' }}</td>
                    <td>
                        @if($municipio->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <x-ui.action-buttons
                            :editUrl="route('fac.catalogos.municipios.edit', $municipio->id_municipio)"
                            :deleteUrl="route('fac.catalogos.municipios.destroy', $municipio->id_municipio)"
                            deleteMessage="¿Deseas desactivar este distrito?"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const urlDepartamentos = "{{ route('fac.catalogos.municipios.departamentos_por_pais') }}";
    const urlMunicipiosMh = "{{ route('fac.catalogos.municipios.municipios_mh_por_departamento') }}";

    const selPais = document.getElementById('filtroPais');
    const selDepto = document.getElementById('filtroDepartamento');
    const selMunicipioMh = document.getElementById('filtroMunicipioMh');

    const selectedDepto = "{{ $idDepartamento ?? '' }}";
    const selectedMunicipioMh = "{{ $idMunicipioMh ?? '' }}";

    if (!selPais || !selDepto || !selMunicipioMh) return;

    function cargarOpciones(select, datos, textoDefault, campoId, campoNombre, selectedValue = '') {
        select.innerHTML = `<option value="">${textoDefault}</option>`;

        datos.forEach(item => {
            const option = document.createElement('option');
            option.value = item[campoId];
            option.textContent = item[campoNombre];

            if (String(item[campoId]) === String(selectedValue)) {
                option.selected = true;
            }

            select.appendChild(option);
        });
    }

    function cargarDepartamentos(idPais, selected = '') {
        selDepto.innerHTML = '<option value="">Todos</option>';
        selMunicipioMh.innerHTML = '<option value="">Todos</option>';

        if (!idPais) return;

        fetch(`${urlDepartamentos}?id_pais=${idPais}`)
            .then(response => response.json())
            .then(data => {
                cargarOpciones(selDepto, data, 'Todos', 'id_departamento', 'nombre_departamento', selected);

                if (selected) {
                    cargarMunicipiosMh(selected, selectedMunicipioMh);
                }
            });
    }

    function cargarMunicipiosMh(idDepartamento, selected = '') {
        selMunicipioMh.innerHTML = '<option value="">Todos</option>';

        if (!idDepartamento) return;

        fetch(`${urlMunicipiosMh}?id_departamento=${idDepartamento}`)
            .then(response => response.json())
            .then(data => {
                cargarOpciones(selMunicipioMh, data, 'Todos', 'id_municipio_mh', 'municipio_mh_nombre', selected);
            });
    }

    selPais.addEventListener('change', function () {
        cargarDepartamentos(this.value);
    });

    selDepto.addEventListener('change', function () {
        cargarMunicipiosMh(this.value);
    });

    if (selPais.value) {
        cargarDepartamentos(selPais.value, selectedDepto);
    }
});
</script>
@endpush

@endsection