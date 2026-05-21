@extends('layouts.app')

@section('title', 'Nuevo tipo de formación | Facilitadores FEPADE')
@section('page-title', 'Nuevo tipo de formación')
@section('page-subtitle', 'Registrar un nuevo tipo de formación')

@section('content')

<x-ui.page-header
    title="Nuevo tipo de formación"
    subtitle="Completa la información del tipo de formación."
/>

<div class="fepade-card">

    <form method="POST"
          action="{{ route('fac.catalogos.tipo-formacion.store') }}">

        @csrf

        <div class="row g-4">

            <div class="col-md-8">

                <label class="form-label">
                    Nombre del tipo de formación
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="nombre"
                    value="{{ old('nombre') }}"
                    class="form-control @error('nombre') is-invalid @enderror"
                    placeholder="Ej: Académica"
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

            <a href="{{ route('fac.catalogos.tipo-formacion.index') }}"
               class="btn btn-outline-secondary">
                Cancelar
            </a>

            <button type="submit"
                    class="btn btn-fepade">
                Guardar tipo de formación
            </button>

        </div>

    </form>

</div>

@endsection