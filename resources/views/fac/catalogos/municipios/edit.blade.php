@extends('layouts.app')

@section('title', 'Editar municipio | Facilitadores FEPADE')
@section('page-title', 'Editar municipio')
@section('page-subtitle', 'Actualizar información del catálogo de municipios')

@section('content')
<x-ui.page-header
    title="Editar municipio"
    subtitle="Actualiza los datos del municipio seleccionado."
/>

<div class="fepade-card">
    <form method="POST" action="{{ route('fac.catalogos.municipios.update', $municipio->id_municipio) }}">
        @csrf
        @method('PUT')
        <div class="row g-4">

            {{-- País --}}
            <div class="col-md-4">
                <label class="form-label">País <span class="text-danger">*</span></label>
                <select
                    name="id_pais"
                    id="id_pais"
                    class="form-select @error('id_pais') is-invalid @enderror"
                >
                    <option value="">— Seleccione —</option>
                    @foreach($paises as $pais)
                        <option value="{{ $pais->id_pais }}"
                            {{ old('id_pais', $municipio->id_pais) == $pais->id_pais ? 'selected' : '' }}>
                            {{ $pais->nombre_pais }}
                        </option>
                    @endforeach
                </select>
                @error('id_pais')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Departamento --}}
            <div class="col-md-4">
                <label class="form-label">Departamento <span class="text-danger">*</span></label>
                <select
                    name="id_departamento"
                    id="id_departamento"
                    class="form-select @error('id_departamento') is-invalid @enderror"
                >
                    <option value="">— Cargando... —</option>
                    @foreach($departamentos as $depto)
                        <option value="{{ $depto->id_departamento }}"
                            {{ old('id_departamento', $municipio->id_departamento) == $depto->id_departamento ? 'selected' : '' }}>
                            {{ $depto->nombre_departamento }}
                        </option>
                    @endforeach
                </select>
                @error('id_departamento')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Municipio MH --}}
            <div class="col-md-4">
                <label class="form-label">Municipio MH</label>
                <select
                    name="id_municipio_mh"
                    id="id_municipio_mh"
                    class="form-select @error('id_municipio_mh') is-invalid @enderror"
                >
                    <option value="">— Cargando... —</option>
                    @foreach($municipiosMh as $mh)
                        <option value="{{ $mh->id_municipio_mh }}"
                            {{ old('id_municipio_mh', $municipio->id_municipio_mh) == $mh->id_municipio_mh ? 'selected' : '' }}>
                            {{ $mh->municipio_mh_nombre }}
                        </option>
                    @endforeach
                </select>
                @error('id_municipio_mh')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Nombre --}}
            <div class="col-md-5">
                <label class="form-label">Nombre del municipio <span class="text-danger">*</span></label>
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

            {{-- Código MH --}}
            <div class="col-md-4">
                <label class="form-label">Código MH</label>
                <input
                    type="text"
                    name="mh_codigo_distrito"
                    value="{{ old('mh_codigo_distrito', $municipio->mh_codigo_distrito) }}"
                    class="form-control @error('mh_codigo_distrito') is-invalid @enderror"
                    placeholder="Ej: 0601"
                >
                @error('mh_codigo_distrito')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Georeferencia --}}
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

            {{-- Estado --}}
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

        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('fac.catalogos.municipios.index') }}" class="btn btn-outline-secondary">
                Cancelar
            </a>
            <button type="submit" class="btn btn-fepade">
                Actualizar municipio
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
const urlDepartamentos = "{{ route('fac.catalogos.municipios.departamentos_por_pais') }}";
const urlMunicipiosMh  = "{{ route('fac.catalogos.municipios.municipios_mh_por_departamento') }}";

const selPais        = document.getElementById('id_pais');
const selDepto       = document.getElementById('id_departamento');
const selMunicipioMh = document.getElementById('id_municipio_mh');

const currentDepto       = "{{ old('id_departamento', $municipio->id_departamento) }}";
const currentMunicipioMh = "{{ old('id_municipio_mh', $municipio->id_municipio_mh) }}";

function cargarOpciones(select, datos, valorDefault, campoId, campoNombre, selectedValue = '') {
    select.innerHTML = `<option value="">${valorDefault}</option>`;
    datos.forEach(item => {
        const opt = document.createElement('option');
        opt.value       = item[campoId];
        opt.textContent = item[campoNombre];
        if (String(item[campoId]) === String(selectedValue)) opt.selected = true;
        select.appendChild(opt);
    });
    select.disabled = datos.length === 0;
}

selPais.addEventListener('change', function () {
    const idPais = this.value;

    selDepto.innerHTML       = '<option value="">— Seleccione —</option>';
    selDepto.disabled        = true;
    selMunicipioMh.innerHTML = '<option value="">— Seleccione un departamento primero —</option>';
    selMunicipioMh.disabled  = true;

    if (!idPais) return;

    fetch(`${urlDepartamentos}?id_pais=${idPais}`)
        .then(r => r.json())
        .then(data => cargarOpciones(selDepto, data, '— Seleccione —', 'id_departamento', 'nombre_departamento'));
});

selDepto.addEventListener('change', function () {
    const idDepto = this.value;

    selMunicipioMh.innerHTML = '<option value="">— Seleccione —</option>';
    selMunicipioMh.disabled  = true;

    if (!idDepto) return;

    fetch(`${urlMunicipiosMh}?id_departamento=${idDepto}`)
        .then(r => r.json())
        .then(data => cargarOpciones(selMunicipioMh, data, '— Seleccione —', 'id_municipio_mh', 'municipio_mh_nombre'));
});

// Precargar departamentos y municipiosMh al cargar la página (edit)
document.addEventListener('DOMContentLoaded', () => {
    const idPais = selPais.value;
    if (!idPais) return;

    fetch(`${urlDepartamentos}?id_pais=${idPais}`)
        .then(r => r.json())
        .then(data => {
            cargarOpciones(selDepto, data, '— Seleccione —', 'id_departamento', 'nombre_departamento', currentDepto);
            if (currentDepto) {
                fetch(`${urlMunicipiosMh}?id_departamento=${currentDepto}`)
                    .then(r => r.json())
                    .then(data2 => cargarOpciones(selMunicipioMh, data2, '— Seleccione —', 'id_municipio_mh', 'municipio_mh_nombre', currentMunicipioMh));
            }
        });
});
</script>
@endpush
@endsection