@extends('layouts.app')

@section('title', 'Nuevo nivel de idioma | Facilitadores FEPADE')
@section('page-title', 'Nuevo nivel de idioma')
@section('page-subtitle', 'Registrar un nuevo nivel de idioma')

@section('content')

<x-ui.page-header title="Nuevo nivel de idioma" subtitle="Completa la información del nivel de idioma." />

<x-ui.page-card title="Datos del nivel de idioma" subtitle="Registra el nombre y estado del nivel de idioma.">
    <form method="POST" action="{{ route('fac.catalogos.idioma-nivel.store') }}">
        @csrf

        <div class="row g-4">
            <div class="col-md-8">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" placeholder="Ej: Avanzado">

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
            :backUrl="route('fac.catalogos.idioma-nivel.index')"
            submitText="Guardar"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection