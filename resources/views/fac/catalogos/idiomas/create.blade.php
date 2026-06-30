@extends('layouts.app')

@section('title', 'Nuevo idioma | Facilitadores FEPADE')
@section('page-title', 'Nuevo idioma')
@section('page-subtitle', 'Registrar un nuevo idioma para el perfil de consultores')

@section('content')

<x-ui.page-header title="Nuevo idioma" subtitle="Completa la información del idioma." />

<x-ui.page-card title="Datos del idioma" subtitle="Registra el nombre y estado del idioma.">
    <form method="POST" action="{{ route('fac.catalogos.idiomas.store') }}">
        @csrf

        <div class="row g-4">
            <div class="col-md-8">
                <label class="form-label">Nombre del idioma <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" placeholder="Ej: Inglés">

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
            :backUrl="route('fac.catalogos.idiomas.index')"
            submitText="Guardar"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection