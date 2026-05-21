@extends('layouts.app')

@section('title', 'Experiencia consultor | Facilitadores FEPADE')
@section('page-title', 'Experiencia del consultor')
@section('page-subtitle', 'Experiencia laboral, disponibilidad, habilidades, idiomas y consultorías')

@section('content')

<x-ui.page-header 
    title="Experiencia del consultor"
    subtitle="{{ $consultor->nombre_completo }}"
/>

@include('fac.consultores.partials._wizard', ['step' => 4, 'consultor' => $consultor])

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Revisa los campos marcados.</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('fac.consultores.experiencia.update', $consultor) }}" enctype="multipart/form-data">
    @csrf

    <div class="fepade-card mb-4">
        <div class="contacto-section-header">
            <div>
                <h4>Experiencia laboral</h4>
                <p>Registra empresas, cargos, periodo laboral, jefe inmediato y evidencia.</p>
            </div>

            <button type="button" class="btn btn-fepade" onclick="agregarExperiencia()">
                + Añadir experiencia
            </button>
        </div>

        <div id="experiencias-wrapper">
            @forelse($consultor->experienciasLaborales as $index => $experiencia)
                @include('fac.consultores.partials._experiencia_laboral_item', [
                    'index' => $index,
                    'experiencia' => $experiencia
                ])
            @empty
                @include('fac.consultores.partials._experiencia_laboral_item', [
                    'index' => 0,
                    'experiencia' => null
                ])
            @endforelse
        </div>
    </div>

    <div class="fepade-card mb-4">
        <div class="contacto-section-header">
            <div>
                <h4>Disponibilidad</h4>
                <p>Selecciona las disponibilidades aplicables al consultor.</p>
            </div>
        </div>

        <div class="row g-3">
            @foreach($catalogos['tiposDisponibilidad'] as $tipo)
                <div class="col-md-4">
                    <div class="form-check">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            name="disponibilidades[]" 
                            value="{{ $tipo->id_tipo_disponibilidad }}"
                            id="disp_{{ $tipo->id_tipo_disponibilidad }}"
                            {{ $consultor->disponibilidades->contains('id_tipo_disponibilidad', $tipo->id_tipo_disponibilidad) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="disp_{{ $tipo->id_tipo_disponibilidad }}">
                            {{ $tipo->nombre }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="fepade-card mb-4">
        <div class="contacto-section-header">
            <div>
                <h4>Habilidades</h4>
                <p>Selecciona áreas de especialización, habilidades técnicas y habilidades blandas.</p>
            </div>
        </div>

        @foreach($catalogos['tiposHabilidad'] as $tipoHabilidad)
            <h6 class="expediente-subtitle mt-3">{{ $tipoHabilidad->nombre }}</h6>

            <div class="row g-3">
                @foreach($catalogos['habilidades']->where('id_tipo_habilidad', $tipoHabilidad->id_tipo_habilidad) as $habilidad)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                name="habilidades[]" 
                                value="{{ $habilidad->id_habilidad }}"
                                id="hab_{{ $habilidad->id_habilidad }}"
                                {{ $consultor->habilidades->contains('id_habilidad', $habilidad->id_habilidad) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="hab_{{ $habilidad->id_habilidad }}">
                                {{ $habilidad->nombre }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <div class="fepade-card mb-4">
        <div class="contacto-section-header">
            <div>
                <h4>Idiomas</h4>
                <p>Registra idiomas, nivel y certificado opcional.</p>
            </div>

            <button type="button" class="btn btn-fepade" onclick="agregarIdioma()">
                + Añadir idioma
            </button>
        </div>

        <div id="idiomas-wrapper">
            @forelse($consultor->idiomas as $index => $idioma)
                @include('fac.consultores.partials._idioma_item', [
                    'index' => $index,
                    'idiomaConsultor' => $idioma,
                    'catalogos' => $catalogos
                ])
            @empty
                @include('fac.consultores.partials._idioma_item', [
                    'index' => 0,
                    'idiomaConsultor' => null,
                    'catalogos' => $catalogos
                ])
            @endforelse
        </div>
    </div>

    <div class="fepade-card mb-4">
        <div class="contacto-section-header">
            <div>
                <h4>Tipos de consultoría</h4>
                <p>Selecciona una o varias áreas en las que el consultor puede apoyar.</p>
            </div>
        </div>

        <div class="row g-3">
            @foreach($catalogos['tiposConsultoria'] as $tipo)
                <div class="col-md-4">
                    <div class="form-check">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            name="tipos_consultoria[]" 
                            value="{{ $tipo->id_tipo_consultoria }}"
                            id="tc_{{ $tipo->id_tipo_consultoria }}"
                            {{ $consultor->tiposConsultoria->contains('id_tipo_consultoria', $tipo->id_tipo_consultoria) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="tc_{{ $tipo->id_tipo_consultoria }}">
                            {{ $tipo->nombre }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('fac.consultores.formacion.edit', $consultor) }}" class="btn btn-outline-secondary">
            Anterior: Formación
        </a>

        <button type="submit" class="btn btn-fepade">
            Guardar y continuar
        </button>
    </div>
</form>

<script>
    let experienciaIndex = {{ max($consultor->experienciasLaborales->count(), 1) }};
    let idiomaIndex = {{ max($consultor->idiomas->count(), 1) }};

    function eliminarBloque(button, selector) {
        button.closest(selector).remove();
    }

    function agregarExperiencia() {
        const wrapper = document.getElementById('experiencias-wrapper');

        wrapper.insertAdjacentHTML('beforeend', `
            @include('fac.consultores.partials._experiencia_laboral_item_js')
        `);

        experienciaIndex++;
    }

    function agregarIdioma() {
        const wrapper = document.getElementById('idiomas-wrapper');

        wrapper.insertAdjacentHTML('beforeend', `
            @include('fac.consultores.partials._idioma_item_js')
        `);

        idiomaIndex++;
    }
</script>

@endsection