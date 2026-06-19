@extends('layouts.app')

@section('title', ($areaEspecializacion->exists ? 'Editar' : 'Nueva') . ' área | Facilitadores FEPADE')
@section('page-title', $areaEspecializacion->exists ? 'Editar área de especialización' : 'Nueva área de especialización')
@section('content')
<x-ui.page-header title="{{ $areaEspecializacion->exists ? 'Editar área de especialización' : 'Nueva área de especialización' }}" subtitle="Completa la información del catálogo." />

<form method="POST" action="{{ $areaEspecializacion->exists ? route('fac.catalogos.area-especializacion.update', $areaEspecializacion) : route('fac.catalogos.area-especializacion.store') }}" class="card-fepade">
    @csrf
    @if($areaEspecializacion->exists) @method('PUT') @endif
    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $areaEspecializacion->nombre) }}" required>
        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $areaEspecializacion->descripcion) }}</textarea>
    </div>
    <div class="form-check form-switch mb-4">
        <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', $areaEspecializacion->activo ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="activo">Activo</label>
    </div>
    <div class="d-flex justify-content-between">
        <a href="{{ route('fac.catalogos.area-especializacion.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-fepade">Guardar</button>
    </div>
</form>
@endsection
