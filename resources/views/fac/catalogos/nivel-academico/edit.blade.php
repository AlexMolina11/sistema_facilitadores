@extends('layouts.app')

@section('title', 'Editar nivel académico | Facilitadores FEPADE')

@section('content')

<div class="fepade-card">

    <form method="POST"
          action="{{ route('fac.catalogos.nivel-academico.update', $nivel_academico) }}">

        @csrf
        @method('PUT')

        <div class="row g-4">

            <div class="col-md-8">

                <label class="form-label">
                    Nombre <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="nombre"
                    value="{{ old('nombre', $nivel_academico->nombre) }}"
                    class="form-control @error('nombre') is-invalid @enderror"
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
                        {{ old('activo', $nivel_academico->activo) ? 'checked' : '' }}
                    >

                    <label class="form-check-label" for="activo">
                        Activo
                    </label>

                </div>

            </div>

        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">

            <a href="{{ route('fac.catalogos.nivel-academico.index') }}"
               class="btn btn-outline-secondary">
                Cancelar
            </a>

            <button type="submit" class="btn btn-fepade">
                Actualizar nivel académico
            </button>

        </div>

    </form>

</div>

@endsection
