@extends('layouts.app')

@section('title', 'Editar idioma | Facilitadores FEPADE')
@section('page-title', 'Editar idioma')
@section('page-subtitle', 'Actualizar información del catálogo de idiomas')

@section('content')

<x-ui.page-header title="Editar idioma" subtitle="Actualiza los datos del idioma seleccionado." />

<x-ui.page-card title="Datos del idioma" subtitle="Modifica el nombre o estado del idioma.">
    <form method="POST" action="{{ route('fac.catalogos.idiomas.update', $idioma) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-8">
                <label class="form-label">Nombre del idioma <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $idioma->nombre) }}" class="form-control @error('nombre') is-invalid @enderror">

                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label d-block">Estado</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', $idioma->activo) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>
        </div>

        <x-ui.form-actions
            :backUrl="route('fac.catalogos.idiomas.index')"
            submitText="Guardar cambios"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection