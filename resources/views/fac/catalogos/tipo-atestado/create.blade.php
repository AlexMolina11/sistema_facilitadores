@extends('layouts.app')

@section('title', 'Nuevo tipo de atestado | Facilitadores FEPADE')
@section('page-title', 'Nuevo tipo de atestado')
@section('page-subtitle', 'Registrar un nuevo tipo de atestado')

@section('content')

<x-ui.page-header
    title="Nuevo tipo de atestado"
    subtitle="Completa la información del tipo de atestado."
/>

<div class="fepade-card">

    <form method="POST"
          action="{{ route('fac.catalogos.tipo-atestado.store') }}">

        @csrf

        <div class="row g-4">

            <div class="col-md-4">

                <label class="form-label">
                    Tipo de formación
                    <span class="text-danger">*</span>
                </label>

                <select
                    name="id_tipo_formacion"
                    class="form-select @error('id_tipo_formacion') is-invalid @enderror"
                >
                    <option value="">Seleccione una opción</option>

                    @foreach($tiposFormacion as $tipoFormacion)
                        <option
                            value="{{ $tipoFormacion->id_tipo_formacion }}"
                            {{ old('id_tipo_formacion') == $tipoFormacion->id_tipo_formacion ? 'selected' : '' }}
                        >
                            {{ $tipoFormacion->nombre }}
                        </option>
                    @endforeach
                </select>

                @error('id_tipo_formacion')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <div class="col-md-5">

                <label class="form-label">
                    Nombre del tipo de atestado
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="nombre"
                    value="{{ old('nombre') }}"
                    class="form-control @error('nombre') is-invalid @enderror"
                    placeholder="Ej: Diploma"
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

            <a href="{{ route('fac.catalogos.tipo-atestado.index') }}"
               class="btn btn-outline-secondary">
                Cancelar
            </a>

            <button type="submit"
                    class="btn btn-fepade">
                Guardar tipo de atestado
            </button>

        </div>

    </form>

</div>

@endsection