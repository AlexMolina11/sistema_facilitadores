@extends('layouts.app')

@section('title', 'Editar opción de sexo | Facilitadores FEPADE')
@section('page-title', 'Editar opción de sexo')
@section('page-subtitle', 'Actualizar información del catálogo')

@section('content')

<x-ui.page-header title="Editar opción de sexo" subtitle="Actualiza la opción seleccionada." />

<x-ui.page-card title="Datos de la opción" subtitle="Modifica el nombre o estado de la opción.">
    <form method="POST" action="{{ route('fac.catalogos.sexo.update', $sexo) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-8">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $sexo->nombre) }}" class="form-control @error('nombre') is-invalid @enderror">

                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label d-block">Estado</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', $sexo->activo) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>
        </div>

        <x-ui.form-actions
            :backUrl="route('fac.catalogos.sexo.index')"
            submitText="Guardar cambios"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection