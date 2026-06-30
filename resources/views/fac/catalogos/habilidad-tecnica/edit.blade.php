@extends('layouts.app')

@section('title', 'Editar habilidad técnica | Facilitadores FEPADE')
@section('page-title', 'Editar habilidad técnica')
@section('page-subtitle', 'Actualizar habilidad técnica vinculada a un área')

@section('content')

<x-ui.page-header 
    title="Editar habilidad técnica" 
    subtitle="Actualiza los datos de la habilidad técnica seleccionada."
/>

<x-ui.page-card 
    title="Datos de la habilidad" 
    subtitle="Modifica el área, nombre, descripción o estado de la habilidad técnica."
>
    <form method="POST" action="{{ route('fac.catalogos.habilidad-tecnica.update', $habilidadTecnica) }}">
        @csrf
        @method('PUT')

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
                            @selected(old('id_area_especializacion', $habilidadTecnica->id_area_especializacion) == $area->id_area_especializacion)
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
                    value="{{ old('nombre', $habilidadTecnica->nombre) }}" 
                    class="form-control @error('nombre') is-invalid @enderror"
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
                >{{ old('descripcion', $habilidadTecnica->descripcion) }}</textarea>

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
                        {{ old('activo', $habilidadTecnica->activo) ? 'checked' : '' }}
                    >

                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>
        </div>

        <x-ui.form-actions
            :backUrl="route('fac.catalogos.habilidad-tecnica.index')"
            submitText="Guardar cambios"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection