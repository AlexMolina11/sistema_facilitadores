@php($habilidadTecnica = new \App\Modules\Fac\Models\HabilidadTecnica())
@extends('layouts.app')

@section('title', ($habilidadTecnica->exists ? 'Editar' : 'Nueva') . ' habilidad técnica | Facilitadores FEPADE')
@section('page-title', $habilidadTecnica->exists ? 'Editar habilidad técnica' : 'Nueva habilidad técnica')
@section('content')
<x-ui.page-header title="{{ $habilidadTecnica->exists ? 'Editar habilidad técnica' : 'Nueva habilidad técnica' }}" subtitle="Vincula la habilidad técnica a un área de especialización." />

<form method="POST" action="{{ $habilidadTecnica->exists ? route('fac.catalogos.habilidad-tecnica.update', $habilidadTecnica) : route('fac.catalogos.habilidad-tecnica.store') }}" class="card-fepade">
    @csrf
    @if($habilidadTecnica->exists) @method('PUT') @endif
    <div class="mb-3">
        <label class="form-label">Área de especialización</label>
        <select name="id_area_especializacion" class="form-select @error('id_area_especializacion') is-invalid @enderror" required>
            <option value="">Seleccione...</option>
            @foreach($areas as $area)
                <option value="{{ $area->id_area_especializacion }}" {{ old('id_area_especializacion', $habilidadTecnica->id_area_especializacion) == $area->id_area_especializacion ? 'selected' : '' }}>{{ $area->nombre }}</option>
            @endforeach
        </select>
        @error('id_area_especializacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $habilidadTecnica->nombre) }}" required>
        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $habilidadTecnica->descripcion) }}</textarea>
    </div>
    <div class="form-check form-switch mb-4">
        <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', $habilidadTecnica->activo ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="activo">Activo</label>
    </div>
    <div class="d-flex justify-content-between">
        <a href="{{ route('fac.catalogos.habilidad-tecnica.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-fepade">Guardar</button>
    </div>
</form>
@endsection
