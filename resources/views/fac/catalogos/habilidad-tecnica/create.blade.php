@extends('layouts.app')

@section('title', 'Nueva habilidad técnica | Facilitadores FEPADE')
@section('page-title', 'Nueva habilidad técnica')
@section('page-subtitle', 'Registrar una habilidad técnica vinculada a un área')

@section('content')

<x-ui.page-header 
    title="Nueva habilidad técnica" 
    subtitle="Completa la información de la habilidad técnica."
/>

<x-ui.page-card 
    title="Datos de la habilidad" 
    subtitle="Registra el área de especialización y el nombre de la habilidad técnica."
>
    <form method="POST" action="{{ route('fac.catalogos.habilidad-tecnica.store') }}">
        @csrf

        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label">
                    Área de especialización <span class="text-danger">*</span>
                </label>

                <select 
                    name="id_area_especializacion" 
                    class="form-select @error('id_area_especializacion') is-invalid @enderror"
                >
                    <option value="">Seleccione...</option>
                    @foreach($areas as $area)
                        <option 
                            value="{{ $area->id_area_especializacion }}" 
                            @selected(old('id_area_especializacion') == $area->id_area_especializacion)
                        >
                            {{ $area->nombre }}
                        </option>
                    @endforeach
                </select>

                @error('id_area_especializacion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">
                    Nombre de la habilidad <span class="text-danger">*</span>
                </label>

                <input 
                    type="text" 
                    name="nombre" 
                    value="{{ old('nombre') }}" 
                    class="form-control @error('nombre') is-invalid @enderror" 
                    placeholder="Ej: Gestión de proyectos con Scrum"
                >

                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-8">
                <label class="form-label">Descripción</label>

                <textarea 
                    name="descripcion" 
                    class="form-control @error('descripcion') is-invalid @enderror" 
                    rows="3" 
                    placeholder="Descripción opcional de la habilidad"
                >{{ old('descripcion') }}</textarea>

                @error('descripcion')
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
        </div>

        <x-ui.form-actions
            :backUrl="route('fac.catalogos.habilidad-tecnica.index')"
            submitText="Guardar"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection