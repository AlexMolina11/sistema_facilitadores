@extends('layouts.app')

@section('title', 'Nuevo tipo de teléfono | Facilitadores FEPADE')
@section('page-title', 'Nuevo tipo de teléfono')
@section('page-subtitle', 'Registrar un nuevo tipo de teléfono en el sistema')

@section('content')
<x-ui.page-header
    title="Nuevo tipo de teléfono"
    subtitle="Completa la información del tipo de teléfono."
/>

<div class="fepade-card">
    <form method="POST" action="{{ route('fac.catalogos.tipo_telefono.store') }}">
        @csrf
        <div class="row g-4">

            <div class="col-md-6">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="nombre"
                    value="{{ old('nombre') }}"
                    class="form-control @error('nombre') is-invalid @enderror"
                    placeholder="Ej: Celular"
                >
                @error('nombre')
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

        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('fac.catalogos.tipo_telefono.index') }}" class="btn btn-outline-secondary">
                Cancelar
            </a>
            <button type="submit" class="btn btn-fepade">
                Guardar tipo
            </button>
        </div>
    </form>
</div>
@endsection