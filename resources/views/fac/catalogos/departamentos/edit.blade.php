@extends('layouts.app')

@section('title', 'Editar departamento | Facilitadores FEPADE')
@section('page-title', 'Editar departamento')
@section('page-subtitle', 'Actualizar información del catálogo de departamentos')

@section('content')

<x-ui.page-header
    title="Editar departamento"
    subtitle="Actualiza los datos del departamento seleccionado."
/>

<x-ui.page-card title="Datos del departamento" subtitle="Modifica el país, nombre, código, georeferencia o estado.">
    <form method="POST" action="{{ route('fac.catalogos.departamentos.update', $departamento->id_departamento) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-4">
                <label class="form-label">País <span class="text-danger">*</span></label>
                <select
                    name="id_pais"
                    class="form-select @error('id_pais') is-invalid @enderror"
                >
                    <option value="">— Seleccione —</option>
                    @foreach($paises as $pais)
                        <option value="{{ $pais->id_pais }}" @selected(old('id_pais', $departamento->id_pais) == $pais->id_pais)>
                            {{ $pais->nombre_pais }}
                        </option>
                    @endforeach
                </select>

                @error('id_pais')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-5">
                <label class="form-label">Nombre del departamento <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="nombre_departamento"
                    value="{{ old('nombre_departamento', $departamento->nombre_departamento) }}"
                    class="form-control @error('nombre_departamento') is-invalid @enderror"
                    placeholder="Ej: San Salvador"
                >

                @error('nombre_departamento')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label class="form-label d-block">Estado</label>
                <div class="form-check form-switch mt-2">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="activo"
                        value="1"
                        id="activo"
                        {{ old('activo', $departamento->activo) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label">Código MH</label>
                <input
                    type="text"
                    name="mh_codigo_depto"
                    value="{{ old('mh_codigo_depto', $departamento->mh_codigo_depto) }}"
                    class="form-control @error('mh_codigo_depto') is-invalid @enderror"
                    placeholder="Ej: 06"
                >

                @error('mh_codigo_depto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-8">
                <label class="form-label">Georeferencia</label>
                <input
                    type="text"
                    name="georeferencia"
                    value="{{ old('georeferencia', $departamento->georeferencia) }}"
                    class="form-control @error('georeferencia') is-invalid @enderror"
                    placeholder="Ej: 13.6929,-89.2182"
                >

                @error('georeferencia')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <x-ui.form-actions
            :backUrl="route('fac.catalogos.departamentos.index')"
            submitText="Actualizar departamento"
            backText="Cancelar"
        />
    </form>
</x-ui.page-card>

@endsection