@extends('layouts.app')

@section('title', 'Editar habilidad | Facilitadores FEPADE')
@section('page-title', 'Editar habilidad')
@section('page-subtitle', 'Actualizar información del catálogo')

@section('content')

<x-ui.page-header
    title="Editar habilidad"
    subtitle="Actualiza la información de la habilidad seleccionada."
/>

<div class="fepade-card">

    <form method="POST"
          action="{{ route('fac.catalogos.habilidad.update', $habilidad) }}">

        @csrf
        @method('PUT')

        <div class="row g-4">

            <div class="col-md-4">

                <label class="form-label">
                    Tipo de habilidad
                    <span class="text-danger">*</span>
                </label>

                <select
                    name="id_tipo_habilidad"
                    class="form-select @error('id_tipo_habilidad') is-invalid @enderror"
                >
                    <option value="">Seleccione una opción</option>

                    @foreach($tiposHabilidad as $tipoHabilidad)
                        <option
                            value="{{ $tipoHabilidad->id_tipo_habilidad }}"
                            {{ old('id_tipo_habilidad', $habilidad->id_tipo_habilidad) == $tipoHabilidad->id_tipo_habilidad ? 'selected' : '' }}
                        >
                            {{ $tipoHabilidad->nombre }}
                        </option>
                    @endforeach
                </select>

                @error('id_tipo_habilidad')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <div class="col-md-5">

                <label class="form-label">
                    Nombre de la habilidad
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="nombre"
                    value="{{ old('nombre', $habilidad->nombre) }}"
                    class="form-control @error('nombre') is-invalid @enderror"
                    placeholder="Ej: Comunicación efectiva"
                >

                @error('nombre')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

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
                        {{ old('activo', $habilidad->activo) ? 'checked' : '' }}
                    >

                    <label class="form-check-label" for="activo">
                        Activo
                    </label>

                </div>

            </div>

        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">

            <a href="{{ route('fac.catalogos.habilidad.index') }}"
               class="btn btn-outline-secondary">
                Cancelar
            </a>

            <button type="submit"
                    class="btn btn-fepade">
                Actualizar habilidad
            </button>

        </div>

    </form>

</div>

@endsection