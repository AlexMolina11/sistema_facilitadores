@extends('layouts.app')

@section('title', 'Nueva área de especialización | Facilitadores FEPADE')
@section('page-title', 'Nueva área de especialización')
@section('page-subtitle', 'Registrar una nueva área para el perfil de consultores')

@section('content')

<x-ui.page-header 
    title="Nueva área de especialización" 
    subtitle="Completa la información del área."
/>

<x-ui.page-card 
    title="Datos del área" 
    subtitle="Registra el nombre y descripción del área de especialización."
>
    <form method="POST" action="{{ route('fac.catalogos.area-especializacion.store') }}">
        @csrf

        <div class="row g-4">
            <div class="col-md-8">
                <label class="form-label">
                    Nombre del área <span class="text-danger">*</span>
                </label>

                <input 
                    type="text" 
                    name="nombre" 
                    value="{{ old('nombre') }}" 
                    class="form-control @error('nombre') is-invalid @enderror" 
                    placeholder="Ej: Gestión de proyectos"
                >

                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label d-block">Estado</label>

                <div class="form-check form-switch mt-2">
                    <input 
                        class="form-check-input" 
                        type="checkbox" 
                        name="activo" 
                        value="1" 
                        id="activo" 
                        {{ old('activo', true) ? 'checked' : '' }}
                    >

                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">Descripción</label>

                <textarea 
                    name="descripcion" 
                    class="form-control @error('descripcion') is-invalid @enderror" 
                    rows="3" 
                    placeholder="Descripción opcional del área"
                >{{ old('descripcion') }}</textarea>

                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <x-ui.form-actions
            :backUrl="route('fac.catalogos.area-especializacion.index')"
            submitText="Guardar"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection