@extends('layouts.app')

@section('title', 'Editar tipo de teléfono | Facilitadores FEPADE')
@section('page-title', 'Editar tipo de teléfono')
@section('page-subtitle', 'Actualizar información del catálogo de tipos de teléfono')

@section('content')
<x-ui.page-header
    title="Editar tipo de teléfono"
    subtitle="Actualiza los datos del tipo de teléfono seleccionado."
/>

<div class="fepade-card">
    <form method="POST" action="{{ route('fac.catalogos.tipo_telefono.update', $tipo->id_tipo_telefono) }}">
        @csrf
        @method('PUT')
        <div class="row g-4">

            <div class="col-md-6">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="nombre"
                    value="{{ old('nombre', $tipo->nombre) }}"
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
                        {{ old('activo', $tipo->activo) ? 'checked' : '' }}
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
                Actualizar tipo
            </button>
        </div>
    </form>
</div>
@endsection