@extends('layouts.app')

@section('title', 'Nuevo tipo de documento | Facilitadores FEPADE')
@section('page-title', 'Nuevo tipo de documento')
@section('page-subtitle', 'Registrar un nuevo tipo de documento')

@section('content')

<x-ui.page-header title="Nuevo tipo de documento" subtitle="Completa la información del tipo de documento." />

<x-ui.page-card title="Datos del tipo de documento" subtitle="Registra el nombre y estado del tipo de documento.">
    <form method="POST" action="{{ route('fac.catalogos.tipo-documento.store') }}">
        @csrf

        <div class="row g-4">
            <div class="col-md-8">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" placeholder="Ej: DUI">

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
            :backUrl="route('fac.catalogos.tipo-documento.index')"
            submitText="Guardar"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection