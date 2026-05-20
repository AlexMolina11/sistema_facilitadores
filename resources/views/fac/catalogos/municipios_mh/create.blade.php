@extends('layouts.app')

@section('title', 'Nuevo municipio MH | Facilitadores FEPADE')
@section('page-title', 'Nuevo municipio MH')
@section('page-subtitle', 'Registrar un nuevo municipio MH en el sistema')

@section('content')
<x-ui.page-header
    title="Nuevo municipio MH"
    subtitle="Completa la información del municipio."
/>

<div class="fepade-card">
    <form method="POST" action="{{ route('fac.catalogos.municipios_mh.store') }}">
        @csrf
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
                            {{ old('id_departamento') == $departamento->id_departamento ? 'selected' : '' }}>
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
                    value="{{ old('municipio_mh_nombre') }}"
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
                        {{ old('activo', true) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label">Código MH</label>
                <input
                    type="text"
                    name="mh_codigo_municipio"
                    value="{{ old('mh_codigo_municipio') }}"
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
                Guardar municipio
            </button>
        </div>
    </form>
</div>
@endsection