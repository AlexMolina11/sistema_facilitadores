@extends('layouts.app')

@section('title', 'Nueva opción de sexo | Facilitadores FEPADE')
@section('page-title', 'Nueva opción de sexo')
@section('page-subtitle', 'Registrar una nueva opción en el catálogo')

@section('content')

<x-ui.page-header title="Nueva opción de sexo" subtitle="Completa la información de la opción." />

<x-ui.page-card title="Datos de la opción" subtitle="Registra el nombre y estado de la opción.">
    <form method="POST" action="{{ route('fac.catalogos.sexo.store') }}">
        @csrf

        <div class="row g-4">
            <div class="col-md-8">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" placeholder="Ej: Masculino">

                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label d-block">Estado</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>
        </div>

        <x-ui.form-actions
            :backUrl="route('fac.catalogos.sexo.index')"
            submitText="Guardar"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection