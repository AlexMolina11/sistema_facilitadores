@extends('layouts.app')

@section('title', 'Editar tipo de referencia | Facilitadores FEPADE')
@section('page-title', 'Editar tipo de referencia')
@section('page-subtitle', 'Actualizar información del catálogo')

@section('content')

<x-ui.page-header
    title="Editar tipo de referencia"
    subtitle="Actualiza la información del tipo de referencia seleccionado."
/>

<div class="fepade-card">

    <form method="POST"
          action="{{ route('fac.catalogos.tipo-referencia.update', $tipoReferencia) }}">

        @csrf
        @method('PUT')

        <div class="row g-4">

            <div class="col-md-8">

                <label class="form-label">
                    Nombre del tipo de referencia
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="nombre"
                    value="{{ old('nombre', $tipoReferencia->nombre) }}"
                    class="form-control @error('nombre') is-invalid @enderror"
                    placeholder="Ej: Referencia Personal"
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
                        {{ old('activo', $tipoReferencia->activo) ? 'checked' : '' }}
                    >

                    <label class="form-check-label" for="activo">
                        Activo
                    </label>

                </div>

            </div>

        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">

            <a href="{{ route('fac.catalogos.tipo-referencia.index') }}"
               class="btn btn-outline-secondary">
                Cancelar
            </a>

            <button type="submit"
                    class="btn btn-fepade">
                Actualizar tipo de referencia
            </button>

        </div>

    </form>

</div>

@endsection