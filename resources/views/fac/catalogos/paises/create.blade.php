@extends('layouts.app')

@section('title', 'Nuevo país | Facilitadores FEPADE')
@section('page-title', 'Nuevo país')
@section('page-subtitle', 'Registrar un nuevo país en el sistema')

@section('content')
<x-ui.page-header
    title="Nuevo país"
    subtitle="Completa la información del país."
/>

<div class="fepade-card">
    <form method="POST" action="{{ route('fac.catalogos.paises.store') }}">
        @csrf
        <div class="row g-4">

            <div class="col-md-6">
                <label class="form-label">Nombre del país <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="nombre_pais"
                    value="{{ old('nombre_pais') }}"
                    class="form-control @error('nombre_pais') is-invalid @enderror"
                    placeholder="Ej: El Salvador"
                >
                @error('nombre_pais')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label">Código del país <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="codigo_pais"
                    value="{{ old('codigo_pais') }}"
                    class="form-control @error('codigo_pais') is-invalid @enderror"
                    placeholder="Ej: SV"
                >
                @error('codigo_pais')
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
                    name="mh_codigo_pais"
                    value="{{ old('mh_codigo_pais') }}"
                    class="form-control @error('mh_codigo_pais') is-invalid @enderror"
                    placeholder="Ej: 101"
                >
                @error('mh_codigo_pais')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Código MH Nuevo</label>
                <input
                    type="text"
                    name="mh_codigo_pais_new"
                    value="{{ old('mh_codigo_pais_new') }}"
                    class="form-control @error('mh_codigo_pais_new') is-invalid @enderror"
                    placeholder="Ej: 101"
                >
                @error('mh_codigo_pais_new')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('fac.catalogos.paises.index') }}" class="btn btn-outline-secondary">
                Cancelar
            </a>
            <button type="submit" class="btn btn-fepade">
                Guardar país
            </button>
        </div>
    </form>
</div>
@endsection