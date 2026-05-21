@extends('layouts.app')

@section('title', 'Nuevo tipo de referencia | Facilitadores FEPADE')
@section('page-title', 'Nuevo tipo de referencia')
@section('page-subtitle', 'Registrar un nuevo tipo de referencia')

@section('content')

<x-ui.page-header
    title="Nuevo tipo de referencia"
    subtitle="Completa la información del tipo de referencia."
/>

<div class="fepade-card">

    <form method="POST"
          action="{{ route('fac.catalogos.tipo-referencia.store') }}">

        @csrf

        <div class="row g-4">

            <div class="col-md-8">

                <label class="form-label">
                    Nombre del tipo de referencia
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="nombre"
                    value="{{ old('nombre') }}"
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
                        {{ old('activo', true) ? 'checked' : '' }}
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
                Guardar tipo de referencia
            </button>

        </div>

    </form>

</div>

@endsection