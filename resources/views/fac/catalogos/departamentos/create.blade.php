@extends('layouts.app')

@section('title', 'Nuevo departamento | Facilitadores FEPADE')
@section('page-title', 'Nuevo departamento')
@section('page-subtitle', 'Registrar un nuevo departamento en el sistema')

@section('content')
<x-ui.page-header
    title="Nuevo departamento"
    subtitle="Completa la información del departamento."
/>

<div class="fepade-card">
    <form method="POST" action="{{ route('fac.catalogos.departamentos.store') }}">
        @csrf
        <div class="row g-4">

            <div class="col-md-4">
                <label class="form-label">País <span class="text-danger">*</span></label>
                <select
                    name="id_pais"
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

            <div class="col-md-5">
                <label class="form-label">Nombre del departamento <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="nombre_departamento"
                    value="{{ old('nombre_departamento') }}"
                    class="form-control @error('nombre_departamento') is-invalid @enderror"
                    placeholder="Ej: San Salvador"
                >
                @error('nombre_departamento')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
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
                    name="mh_codigo_depto"
                    value="{{ old('mh_codigo_depto') }}"
                    class="form-control @error('mh_codigo_depto') is-invalid @enderror"
                    placeholder="Ej: 06"
                >
                @error('mh_codigo_depto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

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

        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('fac.catalogos.departamentos.index') }}" class="btn btn-outline-secondary">
                Cancelar
            </a>
            <button type="submit" class="btn btn-fepade">
                Guardar departamento
            </button>
        </div>
    </form>
</div>
@endsection