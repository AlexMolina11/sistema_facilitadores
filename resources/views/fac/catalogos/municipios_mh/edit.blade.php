@extends('layouts.app')

@section('title', 'Editar municipio MH | Facilitadores FEPADE')
@section('page-title', 'Editar municipio MH')
@section('page-subtitle', 'Actualizar información del catálogo de municipios MH')

@section('content')
<x-ui.page-header
    title="Editar municipio MH"
    subtitle="Actualiza los datos del municipio seleccionado."
/>

<div class="fepade-card">
    <form method="POST" action="{{ route('fac.catalogos.municipios_mh.update', $municipio->id_municipio_mh) }}">
        @csrf
        @method('PUT')
        <div class="row g-4">

            <div class="col-md-5">
                <label class="form-label">Departamento <span class="text-danger">*</span></label>
                <select
                    name="id_departamento"
                    class="form-select @error('id_departamento') is-invalid @enderror"
                >
                    <option value="">— Seleccione —</option>
                    @foreach($departamentos as $departamento)
                        <option value="{{ $departamento->id_departamento }}"
                            {{ old('id_departamento', $municipio->id_departamento) == $departamento->id_departamento ? 'selected' : '' }}>
                            {{ $departamento->nombre_departamento }}
                        </option>
                    @endforeach
                </select>
                @error('id_departamento')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-5">
                <label class="form-label">Nombre del municipio <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="municipio_mh_nombre"
                    value="{{ old('municipio_mh_nombre', $municipio->municipio_mh_nombre) }}"
                    class="form-control @error('municipio_mh_nombre') is-invalid @enderror"
                    placeholder="Ej: San Salvador"
                >
                @error('municipio_mh_nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
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

            <div class="col-md-4">
                <label class="form-label">Código MH</label>
                <input
                    type="text"
                    name="mh_codigo_municipio"
                    value="{{ old('mh_codigo_municipio', $municipio->mh_codigo_municipio) }}"
                    class="form-control @error('mh_codigo_municipio') is-invalid @enderror"
                    placeholder="Ej: 0601"
                >
                @error('mh_codigo_municipio')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('fac.catalogos.municipios_mh.index') }}" class="btn btn-outline-secondary">
                Cancelar
            </a>
            <button type="submit" class="btn btn-fepade">
                Actualizar municipio
            </button>
        </div>
    </form>
</div>
@endsection