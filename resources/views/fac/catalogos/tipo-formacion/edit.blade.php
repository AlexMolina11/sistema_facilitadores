@extends('layouts.app')

@section('title', 'Editar tipo de formación | Facilitadores FEPADE')
@section('page-title', 'Editar tipo de formación')
@section('page-subtitle', 'Actualizar información del tipo de formación')

@section('content')

<x-ui.page-header title="Editar tipo de formación" subtitle="Actualiza los datos del tipo de formación seleccionado." />

<x-ui.page-card title="Datos del tipo de formación" subtitle="Modifica el nombre o estado del tipo de formación.">
    <form method="POST" action="{{ route('fac.catalogos.tipo-formacion.update', $tipoFormacion) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-8">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $tipoFormacion->nombre) }}" class="form-control @error('nombre') is-invalid @enderror">

                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label d-block">Estado</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', $tipoFormacion->activo) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>
        </div>

        <x-ui.form-actions
            :backUrl="route('fac.catalogos.tipo-formacion.index')"
            submitText="Guardar cambios"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection