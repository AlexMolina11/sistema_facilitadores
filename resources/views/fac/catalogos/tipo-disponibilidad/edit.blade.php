@extends('layouts.app')

@section('title', 'Editar tipo de disponibilidad | Facilitadores FEPADE')
@section('page-title', 'Editar tipo de disponibilidad')
@section('page-subtitle', 'Actualizar información del tipo de disponibilidad')

@section('content')

<x-ui.page-header title="Editar tipo de disponibilidad" subtitle="Actualiza los datos del tipo de disponibilidad seleccionado." />

<x-ui.page-card title="Datos del tipo de disponibilidad" subtitle="Modifica el nombre o estado del tipo de disponibilidad.">
    <form method="POST" action="{{ route('fac.catalogos.tipo-disponibilidad.update', $tipo_disponibilidad) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-8">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $tipo_disponibilidad->nombre) }}" class="form-control @error('nombre') is-invalid @enderror">

                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label d-block">Estado</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', $tipo_disponibilidad->activo) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>
        </div>

        <x-ui.form-actions
            :backUrl="route('fac.catalogos.tipo-disponibilidad.index')"
            submitText="Guardar cambios"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection