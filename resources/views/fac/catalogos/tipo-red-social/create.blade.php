@extends('layouts.app')

@section('title', 'Nuevo tipo de red social | Facilitadores FEPADE')
@section('page-title', 'Nuevo tipo de red social')
@section('page-subtitle', 'Registrar un nuevo tipo de red social')

@section('content')

<x-ui.page-header
    title="Nuevo tipo de red social"
    subtitle="Completa la información del tipo de red social."
/>

<div class="fepade-card">

    <form method="POST"
          action="{{ route('fac.catalogos.tipo-red-social.store') }}">

        @csrf

        <div class="row g-4">

            <div class="col-md-5">

                <label class="form-label">
                    Nombre del tipo de red social
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="nombre"
                    value="{{ old('nombre') }}"
                    class="form-control @error('nombre') is-invalid @enderror"
                    placeholder="Ej: Facebook"
                >

                @error('nombre')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <div class="col-md-4">

                <label class="form-label">
                    Ícono
                </label>

                <input
                    type="text"
                    name="icono"
                    value="{{ old('icono') }}"
                    class="form-control @error('icono') is-invalid @enderror"
                    placeholder="Ej: fa-brands fa-facebook"
                >

                @error('icono')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

                <small class="text-muted">
                    Clase CSS del ícono de Font Awesome. Ejemplo: fa-brands fa-linkedin.
                </small>

            </div>

            <div class="col-md-3">

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

            <a href="{{ route('fac.catalogos.tipo-red-social.index') }}"
               class="btn btn-outline-secondary">
                Cancelar
            </a>

            <button type="submit"
                    class="btn btn-fepade">
                Guardar tipo de red social
            </button>

        </div>

    </form>

</div>

@endsection