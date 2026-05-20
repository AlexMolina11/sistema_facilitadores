@extends('layouts.app')

@section('title', 'Nuevo municipio | Facilitadores FEPADE')
@section('page-title', 'Nuevo municipio')
@section('page-subtitle', 'Registrar un nuevo municipio en el sistema')

@section('content')
<x-ui.page-header
    title="Nuevo municipio"
    subtitle="Completa la información del municipio."
/>

<div class="fepade-card">
    <form method="POST" action="{{ route('fac.catalogos.municipios.store') }}">
        @csrf
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
                            {{ old('id_pais') == $pais->id_pais ? 'selected' : '' }}>
                            {{ $pais->nombre_pais }}
                        </option>
                    @endforeach
                </select>
                @error('id_pais')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Departamento (se carga por AJAX) --}}
            <div class="col-md-4">
                <label class="form-label">Departamento <span class="text-danger">*</span></label>
                <select
                    name="id_departamento"
                    id="id_departamento"
                    class="form-select @error('id_departamento') is-invalid @enderror"
                    disabled
                >
                    <option value="">— Seleccione un país primero —</option>
                </select>
                @error('id_departamento')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Municipio MH (se carga por AJAX) --}}
            <div class="col-md-4">
                <label class="form-label">Municipio MH</label>
                <select
                    name="id_municipio_mh"
                    id="id_municipio_mh"
                    class="form-select @error('id_municipio_mh') is-invalid @enderror"
                    disabled
                >
                    <option value="">— Seleccione un departamento primero —</option>
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
                    value="{{ old('nombre_distrito') }}"
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
                    value="{{ old('mh_codigo_distrito') }}"
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
                    value="{{ old('georeferencia') }}"
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
                        {{ old('activo', true) ? 'checked' : '' }}
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
                Guardar municipio
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

const oldDepto       = "{{ old('id_departamento') }}";
const oldMunicipioMh = "{{ old('id_municipio_mh') }}";

function cargarOpciones(select, datos, valorDefault, campoId, campoNombre, oldValue = '') {
    select.innerHTML = `<option value="">${valorDefault}</option>`;
    datos.forEach(item => {
        const opt = document.createElement('option');
        opt.value       = item[campoId];
        opt.textContent = item[campoNombre];
        if (String(item[campoId]) === String(oldValue)) opt.selected = true;
        select.appendChild(opt);
    });
    select.disabled = datos.length === 0;
}

selPais.addEventListener('change', function () {
    const idPais = this.value;

    // Resetear combos dependientes
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

// Restaurar selecciones tras error de validación
window.addEventListener('DOMContentLoaded', () => {
    if (selPais.value) selPais.dispatchEvent(new Event('change'));
});

selDepto.addEventListener('change', function() {}, false);

document.addEventListener('DOMContentLoaded', () => {
    if ("{{ old('id_pais') }}" && selPais.value) {
        fetch(`${urlDepartamentos}?id_pais=${selPais.value}`)
            .then(r => r.json())
            .then(data => {
                cargarOpciones(selDepto, data, '— Seleccione —', 'id_departamento', 'nombre_departamento', oldDepto);
                if (oldDepto) {
                    fetch(`${urlMunicipiosMh}?id_departamento=${oldDepto}`)
                        .then(r => r.json())
                        .then(data2 => cargarOpciones(selMunicipioMh, data2, '— Seleccione —', 'id_municipio_mh', 'municipio_mh_nombre', oldMunicipioMh));
                }
            });
    }
});
</script>
@endpush
@endsection