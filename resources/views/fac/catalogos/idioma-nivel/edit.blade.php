@extends('layouts.app')

@section('title', 'Editar nivel de idioma | Facilitadores FEPADE')
@section('page-title', 'Editar nivel de idioma')
@section('page-subtitle', 'Actualizar información del nivel de idioma')

@section('content')

<x-ui.page-header title="Editar nivel de idioma" subtitle="Actualiza los datos del nivel de idioma seleccionado." />

<x-ui.page-card title="Datos del nivel de idioma" subtitle="Modifica el nombre o estado del nivel de idioma.">
    <form method="POST" action="{{ route('fac.catalogos.idioma-nivel.update', $idioma_nivel) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-8">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $idioma_nivel->nombre) }}" class="form-control @error('nombre') is-invalid @enderror">

                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label d-block">Estado</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', $idioma_nivel->activo) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>
        </div>

        <x-ui.form-actions
            :backUrl="route('fac.catalogos.idioma-nivel.index')"
            submitText="Guardar cambios"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection