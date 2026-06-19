@extends('layouts.app')

@section('title', 'Editar área de especialización | Facilitadores FEPADE')
@section('page-title', 'Editar área de especialización')
@section('page-subtitle', 'Actualizar información del catálogo de áreas')

@section('content')

<x-ui.page-header title="Editar área de especialización" subtitle="Actualiza los datos del área seleccionada." />

<div class="fepade-card">
    <form method="POST" action="{{ route('fac.catalogos.area-especializacion.update', $areaEspecializacion) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-8">
                <label class="form-label">Nombre del área <span class="text-danger">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $areaEspecializacion->nombre) }}" class="form-control @error('nombre') is-invalid @enderror">
                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label d-block">Estado</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', $areaEspecializacion->activo) ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3">{{ old('descripcion', $areaEspecializacion->descripcion) }}</textarea>
                @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('fac.catalogos.area-especializacion.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-fepade">Actualizar área</button>
        </div>
    </form>
</div>

@endsection
