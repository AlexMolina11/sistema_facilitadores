@extends('layouts.app')

@section('title', 'Editar tipo de habilidad | Facilitadores FEPADE')
@section('page-title', 'Editar tipo de habilidad')
@section('page-subtitle', 'Actualizar información del catálogo')

@section('content')

<x-ui.page-header
    title="Editar tipo de habilidad"
    subtitle="Actualiza la información del tipo de habilidad seleccionado."
/>

<div class="fepade-card">

    <form method="POST"
          action="{{ route('fac.catalogos.tipo-habilidad.update', $tipoHabilidad) }}">

        @csrf
        @method('PUT')

        <div class="row g-4">

            <div class="col-md-8">

                <label class="form-label">
                    Nombre del tipo de habilidad
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="nombre"
                    value="{{ old('nombre', $tipoHabilidad->nombre) }}"
                    class="form-control @error('nombre') is-invalid @enderror"
                    placeholder="Ej: Técnica"
                >

                @error('nombre')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <div class="col-md-4">

                <label class="form-label d-block">
                    Estado
                </label>

                <div class="form-check form-switch mt-2">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="activo"
                        value="1"
                        id="activo"
                        {{ old('activo', $tipoHabilidad->activo) ? 'checked' : '' }}
                    >

                    <label class="form-check-label" for="activo">
                        Activo
                    </label>

                </div>

            </div>

        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">

            <a href="{{ route('fac.catalogos.tipo-habilidad.index') }}"
               class="btn btn-outline-secondary">
                Cancelar
            </a>

            <button type="submit"
                    class="btn btn-fepade">
                Actualizar tipo de habilidad
            </button>

        </div>

    </form>

</div>

@endsection