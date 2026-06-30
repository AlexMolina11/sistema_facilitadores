@extends('layouts.app')

@section('title', 'Editar nivel académico | Facilitadores FEPADE')
@section('page-title', 'Editar nivel académico')
@section('page-subtitle', 'Actualizar información del nivel académico')

@section('content')

<x-ui.page-header title="Editar nivel académico" subtitle="Actualiza los datos del nivel académico seleccionado." />

<x-ui.page-card title="Datos del nivel académico" subtitle="Modifica el nombre o estado del nivel académico.">
    <form method="POST" action="{{ route('fac.catalogos.nivel-academico.update', $nivel_academico) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-8">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $nivel_academico->nombre) }}" class="form-control @error('nombre') is-invalid @enderror">

                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label d-block">Estado</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', $nivel_academico->activo) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>
        </div>

        <x-ui.form-actions
            :backUrl="route('fac.catalogos.nivel-academico.index')"
            submitText="Guardar cambios"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection