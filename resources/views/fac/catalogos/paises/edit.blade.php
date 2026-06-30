@extends('layouts.app')

@section('title', 'Editar país | Facilitadores FEPADE')
@section('page-title', 'Editar país')
@section('page-subtitle', 'Actualizar información del país')

@section('content')

<x-ui.page-header title="Editar país" subtitle="Actualiza los datos del país seleccionado." />

<x-ui.page-card title="Datos del país" subtitle="Modifica la información registrada del país.">
    <form method="POST" action="{{ route('fac.catalogos.paises.update', $pais->id_pais) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-4">
                <label class="form-label">Código de país <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="codigo_pais"
                    value="{{ old('codigo_pais', $pais->codigo_pais) }}"
                    class="form-control @error('codigo_pais') is-invalid @enderror"
                    placeholder="Ej: SV"
                >
                @error('codigo_pais')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-8">
                <label class="form-label">Nombre del país <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="nombre_pais"
                    value="{{ old('nombre_pais', $pais->nombre_pais) }}"
                    class="form-control @error('nombre_pais') is-invalid @enderror"
                    placeholder="Ej: El Salvador"
                >
                @error('nombre_pais')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Código MH</label>
                <input
                    type="text"
                    name="mh_codigo_pais"
                    value="{{ old('mh_codigo_pais', $pais->mh_codigo_pais) }}"
                    class="form-control @error('mh_codigo_pais') is-invalid @enderror"
                >
                @error('mh_codigo_pais')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Código MH nuevo</label>
                <input
                    type="text"
                    name="mh_codigo_pais_new"
                    value="{{ old('mh_codigo_pais_new', $pais->mh_codigo_pais_new) }}"
                    class="form-control @error('mh_codigo_pais_new') is-invalid @enderror"
                >
                @error('mh_codigo_pais_new')
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
                        {{ old('activo', $pais->activo) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>
        </div>

        <x-ui.form-actions
            :backUrl="route('fac.catalogos.paises.index')"
            submitText="Guardar cambios"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection