@extends('layouts.app')

@section('title', 'Búsqueda Avanzada | Facilitadores FEPADE')

@section('page-title', 'Búsqueda de Consultores')

@section('page-subtitle', 'Encuentra al profesional ideal para tus necesidades.')

@section('content')

<x-ui.page-header
    title="Búsqueda de Consultores"
    subtitle="Encuentra al profesional ideal para tus necesidades."
/>

<div class="fepade-card mb-4">

    <div class="row align-items-center">

        <div class="col-md-12">

            <form method="GET" action="#">

                <div class="input-group">

                    <input
                        type="text"
                        class="form-control"
                        name="buscar"
                        placeholder="Buscar por nombre, apellido o documento..."
                        value="{{ request('buscar') }}"
                    >

                    <button
                        class="btn btn-fepade"
                        type="submit"
                    >
                        <i class="fas fa-search"></i>
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<div class="row">

    {{-- PANEL DE FILTROS --}}
    <div class="col-lg-3">

        <div class="fepade-card">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h6 class="fw-bold text-fepade mb-0">
                    <i class="fas fa-filter me-2"></i>
                    Filtros Avanzados
                </h6>

                <button
                    class="btn btn-sm btn-outline-secondary"
                    type="reset"
                >
                    Limpiar
                </button>

            </div>

            <form method="GET" action="#">

                <div class="accordion" id="accordionFiltros">

                    {{-- ACTIVIDAD --}}
                    <div class="accordion-item">

                        <h2 class="accordion-header">

                            <button
                                class="accordion-button"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#actividad"
                            >
                                Actividad
                            </button>

                        </h2>

                        <div
                            id="actividad"
                            class="accordion-collapse collapse show"
                            data-bs-parent="#accordionFiltros"
                        >

                            <div class="accordion-body">

                                <div class="mb-3">

                                    <label class="form-label">
                                        Desde
                                    </label>

                                    <input
                                        type="date"
                                        class="form-control"
                                        name="fecha_desde"
                                    >

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">
                                        Hasta
                                    </label>

                                    <input
                                        type="date"
                                        class="form-control"
                                        name="fecha_hasta"
                                    >

                                </div>

                                <div class="d-flex flex-wrap gap-1">

                                    <button class="btn btn-sm btn-outline-secondary">
                                        Hoy
                                    </button>

                                    <button class="btn btn-sm btn-outline-secondary">
                                        Últimos 7 días
                                    </button>

                                    <button class="btn btn-sm btn-outline-secondary">
                                        Últimos 30 días
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- HABILIDADES --}}
                    <div class="accordion-item">

                        <h2 class="accordion-header">

                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#habilidades"
                            >
                                Habilidades y Áreas
                            </button>

                        </h2>

                        <div
                            id="habilidades"
                            class="accordion-collapse collapse"
                            data-bs-parent="#accordionFiltros"
                        >

                            <div class="accordion-body">

                                <div class="mb-3">

                                    <label class="form-label">
                                        Área de especialización
                                    </label>

                                    <select name="area_especializacion" class="form-select">
                                        <option value="">
                                            Todas
                                        </option>

                                        @foreach($areaEspecializacion ?? [] as $area)

                                            <option 
                                                value="{{ $area->id_habilidad }}"
                                                {{ request('area_especializacion') == $area->id_habilidad ? 'selected' : ''}}
                                            >
                                                {{ $area->nombre }}
                                            </option>

                                        @endforeach

                                    </select>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">
                                        Habilidades Blandas
                                    </label>

                                    <select class="form-select">
                                        <option value="">
                                            Seleccione
                                        </option>
                                    </select>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">
                                        Habilidades técnicas
                                    </label>

                                    <select class="form-select">
                                        <option value="">
                                            Seleccione
                                        </option>
                                    </select>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- EDUCACION --}}
                    <div class="accordion-item">

                        <h2 class="accordion-header">

                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#educacion"
                            >
                                Educación
                            </button>

                        </h2>

                        <div
                            id="educacion"
                            class="accordion-collapse collapse"
                            data-bs-parent="#accordionFiltros"
                        >

                            <div class="accordion-body">

                                {{-- Tipo Formación --}}
                                <div class="mb-3">

                                    <label class="form-label">
                                        Tipo de Formación
                                    </label>

                                    <select
                                        name="tipo_formacion"
                                        class="form-select"
                                    >
                                        <option value="">
                                            Todas
                                        </option>

                                        @foreach($tiposFormacion ?? [] as $tipo)

                                            <option
                                                value="{{ $tipo->id_tipo_formacion }}"
                                                {{ request('tipo_formacion') == $tipo->id_tipo_formacion ? 'selected' : '' }}
                                            >
                                                {{ $tipo->nombre }}
                                            </option>

                                        @endforeach

                                    </select>

                                </div>

                                {{-- Nivel Académico --}}
                                <div class="mb-3">

                                    <label class="form-label">
                                        Nivel Académico
                                    </label>

                                    <select
                                        name="nivel_academico"
                                        class="form-select"
                                    >
                                        <option value="">
                                            Todos
                                        </option>

                                        @foreach($nivelesAcademicos ?? [] as $nivel)

                                            <option
                                                value="{{ $nivel->id_nivel_academico }}"
                                                {{ request('nivel_academico') == $nivel->id_nivel_academico ? 'selected' : '' }}
                                            >
                                                {{ $nivel->nombre }}
                                            </option>

                                        @endforeach

                                    </select>

                                </div>

                                {{-- Tipo de Atestado --}}
                                <div class="mb-3">

                                    <label class="form-label">
                                        Tipo de Atestado
                                    </label>

                                    <select
                                        name="tipo_atestado"
                                        class="form-select"
                                    >
                                        <option value="">
                                            Todos
                                        </option>

                                        @foreach($tiposAtestado ?? [] as $atestado)

                                            <option
                                                value="{{ $atestado->id_tipo_atestado }}"
                                                {{ request('tipo_atestado') == $atestado->id_tipo_atestado ? 'selected' : '' }}
                                            >
                                                {{ $atestado->nombre }}
                                            </option>

                                        @endforeach

                                    </select>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- DATOS DEMOGRAFICOS --}}
                    <div class="accordion-item">

                        <h2 class="accordion-header">

                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#demograficos"
                            >
                                Datos Demográficos
                            </button>

                        </h2>

                        <div
                            id="demograficos"
                            class="accordion-collapse collapse"
                            data-bs-parent="#accordionFiltros"
                        >

                            <div class="accordion-body">

                                <div class="mb-3">

                                    <label class="form-label">
                                        Sexo
                                    </label>

                                    <select class="form-select">
                                        <option value="">
                                            Todos
                                        </option>
                                    </select>

                                </div>

                                <div class="row">

                                    <div class="col">

                                        <input
                                            type="number"
                                            class="form-control"
                                            placeholder="Edad mínima"
                                        >

                                    </div>

                                    <div class="col">

                                        <input
                                            type="number"
                                            class="form-control"
                                            placeholder="Edad máxima"
                                        >

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- UBICACION --}}
                    <div class="accordion-item">

                        <h2 class="accordion-header">

                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#ubicacion"
                            >
                                Ubicación y Disponibilidad
                            </button>

                        </h2>

                        <div
                            id="ubicacion"
                            class="accordion-collapse collapse"
                            data-bs-parent="#accordionFiltros"
                        >

                            <div class="accordion-body">

                                <div class="mb-3">
                                    <label class="form-label">
                                        Departamento
                                    </label>
                                    <select class="form-select"></select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Municipio
                                    </label>
                                    <select class="form-select"></select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Disponibilidad
                                    </label>
                                    <select class="form-select"></select>
                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- IDIOMAS --}}
                    <div class="accordion-item">

                        <h2 class="accordion-header">

                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#idiomas"
                            >
                                Idiomas
                            </button>

                        </h2>

                        <div
                            id="idiomas"
                            class="accordion-collapse collapse"
                            data-bs-parent="#accordionFiltros"
                        >

                            <div class="accordion-body">

                                <div class="mb-3">

                                    <label class="form-label">
                                        Idioma
                                    </label>

                                    <select class="form-select"></select>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">
                                        Nivel
                                    </label>

                                    <select class="form-select"></select>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- EXPERIENCIA --}}
                    <div class="accordion-item">

                        <h2 class="accordion-header">

                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#experiencia"
                            >
                                Experiencia Profesional
                            </button>

                        </h2>

                        <div
                            id="experiencia"
                            class="accordion-collapse collapse"
                            data-bs-parent="#accordionFiltros"
                        >

                            <div class="accordion-body">

                                <div class="mb-3">

                                    <label class="form-label">
                                        Cargo
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                    >

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">
                                        Empresa
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                    >

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="d-grid mt-4">

                    <button
                        class="btn btn-fepade"
                        type="submit"
                    >
                        <i class="fas fa-search me-2"></i>
                        Aplicar Filtros
                    </button>

                </div>

            </form>

        </div>

    </div>

    {{-- RESULTADOS --}}
    <div class="col-lg-9">

        <div class="row g-3">

            @forelse($consultores ?? [] as $consultor)

                <div class="col-md-6 col-xl-4">

                    <div class="fepade-card h-100">

                        <div class="text-center mb-3">

                            <img
                                src="{{ $consultor->foto_url ?? asset('images/avatar-default.png') }}"
                                class="rounded-circle"
                                width="70"
                                height="70"
                            >

                        </div>

                        <h6 class="fw-bold text-fepade mb-1">
                            {{ $consultor->nombre_completo }}
                        </h6>

                        <small class="text-muted">
                            {{ $consultor->nivel_academico }}
                        </small>

                        <hr>

                        <div class="small">

                            <div class="mb-2">
                                <i class="fas fa-clock me-2"></i>
                                {{ $consultor->disponibilidad }}
                            </div>

                            <div class="mb-2">
                                <i class="fas fa-phone me-2"></i>
                                {{ $consultor->telefono }}
                            </div>

                            <div class="mb-2">
                                <i class="fas fa-language me-2"></i>
                                {{ $consultor->idiomas }}
                            </div>

                        </div>

                        <a
                            href="#"
                            class="btn btn-outline-secondary btn-sm mt-3"
                        >
                            Ver Perfil
                        </a>

                    </div>

                </div>

            @empty

                <div class="col-12">

                    <div class="alert alert-info">

                        No se encontraron consultores para los criterios seleccionados.

                    </div>

                </div>

            @endforelse

        </div>

       
 @if(isset($consultores) && method_exists($consultores, 'links'))
    <div class="mt-4">
        {{ $consultores->links('pagination::bootstrap-5') }}
    </div>
@endif
    </div>

</div>

@endsection