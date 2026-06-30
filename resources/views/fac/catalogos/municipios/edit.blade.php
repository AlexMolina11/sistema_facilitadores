@extends('layouts.app')

@section('title', 'Editar distrito | Facilitadores FEPADE')
@section('page-title', 'Editar distrito')
@section('page-subtitle', 'Actualizar información del catálogo de distritos')

@section('content')

<x-ui.page-header
    title="Editar distrito"
    subtitle="Actualiza los datos del distrito seleccionado."
/>

<x-ui.page-card title="Datos del distrito" subtitle="Modifica país, departamento, municipio, distrito, código, georeferencia o estado.">
    <form method="POST" action="{{ route('fac.catalogos.municipios.update', $municipio->id_municipio) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-4">
                <label class="form-label">País <span class="text-danger">*</span></label>
                <select
                    name="id_pais"
                    id="id_pais"
                    class="form-select @error('id_pais') is-invalid @enderror"
                >
                    <option value="">— Seleccione —</option>
                    @foreach($paises as $pais)
                        <option value="{{ $pais->id_pais }}" @selected(old('id_pais', $municipio->id_pais) == $pais->id_pais)>
                            {{ $pais->nombre_pais }}
                        </option>
                    @endforeach
                </select>

                @error('id_pais')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Departamento <span class="text-danger">*</span></label>
                <select
                    name="id_departamento"
                    id="id_departamento"
                    class="form-select @error('id_departamento') is-invalid @enderror"
                >
                    <option value="">— Cargando... —</option>
                    @foreach($departamentos as $depto)
                        <option value="{{ $depto->id_departamento }}" @selected(old('id_departamento', $municipio->id_departamento) == $depto->id_departamento)>
                            {{ $depto->nombre_departamento }}
                        </option>
                    @endforeach
                </select>

                @error('id_departamento')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Municipio</label>
                <select
                    name="id_municipio_mh"
                    id="id_municipio_mh"
                    class="form-select @error('id_municipio_mh') is-invalid @enderror"
                >
                    <option value="">— Cargando... —</option>
                    @foreach($municipiosMh as $mh)
                        <option value="{{ $mh->id_municipio_mh }}" @selected(old('id_municipio_mh', $municipio->id_municipio_mh) == $mh->id_municipio_mh)>
                            {{ $mh->municipio_mh_nombre }}
                        </option>
                    @endforeach
                </select>

                @error('id_municipio_mh')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-5">
                <label class="form-label">Nombre del distrito <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="nombre_distrito"
                    value="{{ old('nombre_distrito', $municipio->nombre_distrito) }}"
                    class="form-control @error('nombre_distrito') is-invalid @enderror"
                    placeholder="Ej: San Salvador"
                >

                @error('nombre_distrito')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label">Código MH</label>
                <input
                    type="text"
                    name="mh_codigo_distrito"
                    value="{{ old('mh_codigo_distrito', $municipio->mh_codigo_distrito) }}"
                    class="form-control @error('mh_codigo_distrito') is-invalid @enderror"
                    placeholder="Ej: 060101"
                >

                @error('mh_codigo_distrito')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label d-block">Estado</label>
                <div class="form-check form-switch mt-2">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="activo"
                        value="1"
                        id="activo"
                        {{ old('activo', $municipio->activo) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>

            <div class="col-md-8">
                <label class="form-label">Georeferencia</label>
                <input
                    type="text"
                    name="georeferencia"
                    value="{{ old('georeferencia', $municipio->georeferencia) }}"
                    class="form-control @error('georeferencia') is-invalid @enderror"
                    placeholder="Ej: 13.6929,-89.2182"
                >

                @error('georeferencia')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <x-ui.form-actions
            :backUrl="route('fac.catalogos.municipios.index')"
            submitText="Actualizar distrito"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const urlDepartamentos = "{{ route('fac.catalogos.municipios.departamentos_por_pais') }}";
    const urlMunicipiosMh = "{{ route('fac.catalogos.municipios.municipios_mh_por_departamento') }}";

    const selPais = document.getElementById('id_pais');
    const selDepto = document.getElementById('id_departamento');
    const selMunicipioMh = document.getElementById('id_municipio_mh');

    const currentDepto = "{{ old('id_departamento', $municipio->id_departamento) }}";
    const currentMunicipioMh = "{{ old('id_municipio_mh', $municipio->id_municipio_mh) }}";

    if (!selPais || !selDepto || !selMunicipioMh) return;

    function cargarOpciones(select, datos, valorDefault, campoId, campoNombre, selectedValue = '') {
        select.innerHTML = `<option value="">${valorDefault}</option>`;

        datos.forEach(item => {
            const option = document.createElement('option');
            option.value = item[campoId];
            option.textContent = item[campoNombre];

            if (String(item[campoId]) === String(selectedValue)) {
                option.selected = true;
            }

            select.appendChild(option);
        });

        select.disabled = datos.length === 0;
    }

    function cargarDepartamentos(idPais, selected = '') {
        selDepto.innerHTML = '<option value="">— Seleccione —</option>';
        selDepto.disabled = true;

        selMunicipioMh.innerHTML = '<option value="">— Seleccione un departamento primero —</option>';
        selMunicipioMh.disabled = true;

        if (!idPais) return;

        fetch(`${urlDepartamentos}?id_pais=${idPais}`)
            .then(response => response.json())
            .then(data => {
                cargarOpciones(selDepto, data, '— Seleccione —', 'id_departamento', 'nombre_departamento', selected);

                if (selected) {
                    cargarMunicipiosMh(selected, currentMunicipioMh);
                }
            });
    }

    function cargarMunicipiosMh(idDepto, selected = '') {
        selMunicipioMh.innerHTML = '<option value="">— Seleccione —</option>';
        selMunicipioMh.disabled = true;

        if (!idDepto) return;

        fetch(`${urlMunicipiosMh}?id_departamento=${idDepto}`)
            .then(response => response.json())
            .then(data => {
                cargarOpciones(selMunicipioMh, data, '— Seleccione —', 'id_municipio_mh', 'municipio_mh_nombre', selected);
            });
    }

    selPais.addEventListener('change', function () {
        cargarDepartamentos(this.value);
    });

    selDepto.addEventListener('change', function () {
        cargarMunicipiosMh(this.value);
    });

    if (selPais.value) {
        cargarDepartamentos(selPais.value, currentDepto);
    }
});
</script>
@endpush

@endsection