@extends('layouts.app')

@section('title', 'Nuevo nivel académico | Facilitadores FEPADE')
@section('page-title', 'Nuevo nivel académico')
@section('page-subtitle', 'Registrar un nuevo nivel académico')

@section('content')

<x-ui.page-header title="Nuevo nivel académico" subtitle="Completa la información del nivel académico." />

<x-ui.page-card title="Datos del nivel académico" subtitle="Registra el nombre y estado del nivel académico.">
    <form method="POST" action="{{ route('fac.catalogos.nivel-academico.store') }}">
        @csrf

        <div class="row g-4">
            <div class="col-md-8">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" placeholder="Ej: Licenciatura">

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
            :backUrl="route('fac.catalogos.nivel-academico.index')"
            submitText="Guardar"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection