@extends('layouts.app')

@section('title', 'Editar tipo de referencia | Facilitadores FEPADE')
@section('page-title', 'Editar tipo de referencia')
@section('page-subtitle', 'Actualizar información del tipo de referencia')

@section('content')

<x-ui.page-header title="Editar tipo de referencia" subtitle="Actualiza los datos del tipo de referencia seleccionado." />

<x-ui.page-card title="Datos del tipo de referencia" subtitle="Modifica el nombre o estado del tipo de referencia.">
    <form method="POST" action="{{ route('fac.catalogos.tipo-referencia.update', $tipoReferencia) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-8">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $tipoReferencia->nombre) }}" class="form-control @error('nombre') is-invalid @enderror">

                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label d-block">Estado</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', $tipoReferencia->activo) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>
        </div>

        <x-ui.form-actions
            :backUrl="route('fac.catalogos.tipo-referencia.index')"
            submitText="Guardar cambios"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection