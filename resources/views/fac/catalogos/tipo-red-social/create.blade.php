@extends('layouts.app')

@section('title', 'Nuevo tipo de red social | Facilitadores FEPADE')
@section('page-title', 'Nuevo tipo de red social')
@section('page-subtitle', 'Registrar un nuevo tipo de red social')

@section('content')

<x-ui.page-header title="Nuevo tipo de red social" subtitle="Completa la información del tipo de red social." />

<x-ui.page-card title="Datos del tipo de red social" subtitle="Registra el nombre, icono y estado del tipo de red social.">
    <form method="POST" action="{{ route('fac.catalogos.tipo-red-social.store') }}">
        @csrf

        <div class="row g-4">
            <div class="col-md-5">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="nombre"
                    value="{{ old('nombre') }}"
                    class="form-control @error('nombre') is-invalid @enderror"
                    placeholder="Ej: LinkedIn"
                >

                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Icono</label>
                <input
                    type="text"
                    name="icono"
                    value="{{ old('icono') }}"
                    class="form-control @error('icono') is-invalid @enderror"
                    placeholder="Ej: fa-brands fa-linkedin"
                >

                @error('icono')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <small class="text-muted">Clase Font Awesome opcional.</small>
            </div>

            <div class="col-md-3">
                <label class="form-label d-block">Estado</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>
        </div>

        <x-ui.form-actions
            :backUrl="route('fac.catalogos.tipo-red-social.index')"
            submitText="Guardar"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection