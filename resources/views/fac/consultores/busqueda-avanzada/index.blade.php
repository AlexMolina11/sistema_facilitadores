@extends('layouts.app')

@section('title', 'Búsqueda avanzada | Facilitadores FEPADE')
@section('page-title', 'Búsqueda avanzada')
@section('page-subtitle', 'Filtra consultores por perfil profesional, experiencia, ubicación, formación, idiomas y disponibilidad.')

@section('content')

@php
    $filtros = $filtros ?? request()->all();
    $totalResultados = $totalConsultores ?? ($consultores->total() ?? 0);
    $busquedaGeneral = $filtros['q'] ?? request('q');

    /**
     * El contador de filtros activos no incluye la búsqueda general,
     * porque esta se muestra como indicador independiente en el hero.
     * Tampoco incluye fecha_tipo, porque es solo el selector de criterio
     * y no representa un filtro aplicado por sí mismo.
     */
    $camposIgnoradosConteo = ['page', 's', '_token', 'q', 'fecha_tipo'];

    $filtrosActivos = collect($filtros)
        ->reject(fn ($value, $key) => in_array($key, $camposIgnoradosConteo, true))
        ->reduce(function ($total, $value) {
            if (is_array($value)) {
                return $total + collect($value)->filter(fn ($item) => filled($item))->count();
            }

            return $total + (filled($value) ? 1 : 0);
        }, 0);
@endphp

<!--<x-ui.page-header
    title="Búsqueda avanzada"
    subtitle="Encuentra consultores por perfil profesional, experiencia, ubicación, formación, idiomas y disponibilidad."
/>-->

<form method="GET" action="{{ route('fac.busqueda.index') }}" id="formBusquedaAvanzada" data-url-base="{{ route('fac.busqueda.index') }}">
    <div class="busqueda-hero-BA busqueda-toolbar mb-4 text-center">
        <div class="busqueda-search-box">
            <span class="busqueda-hero-icon">
                <i class="fa-solid fa-magnifying-glass-chart"></i>
            </span>

            <div class="busqueda-search-input">
                <h2>Banco de consultores FEPADE</h2>
                <input
                    type="text"
                    class="form-control-BA"
                    name="q"
                    placeholder="Nombre, apellido, documento, NIT, NRC o correo..."
                    value="{{ $filtros['q'] ?? '' }}"
                >
            </div>

            <div class="busqueda-search-actions">
                <button class="btn btn-fepade-BA" type="submit">
                    <i class="fas fa-search me-1"></i> Buscar
                </button>

                <a href="{{ route('fac.busqueda.index') }}" class="btn btn-outline-secondary">
                    Limpiar filtros
                </a>
            </div>
        </div>
    </div>

    <div class="busqueda-active-summary fepade-card mb-4" id="busquedaActiveSummary">
        <div class="busqueda-active-summary-header">
            <div>
                <span class="busqueda-section-kicker">Criterios aplicados</span>
                <h5>Resumen de búsqueda</h5>
            </div>
            <span class="busqueda-active-search" id="busquedaGeneralTexto">
                {{ filled($busquedaGeneral) ? 'Búsqueda: ' . $busquedaGeneral : 'Sin búsqueda general' }}
            </span>
        </div>

        <div class="busqueda-active-chip-row" id="busquedaActiveChips">
            <span class="busqueda-active-empty">Sin filtros avanzados activos.</span>
        </div>
    </div>

    <div class="busqueda-layout">
        <aside class="busqueda-filtros fepade-card">
            <div class="busqueda-filtros-header mb-3">
                <div>
                    <h5>Filtros avanzados</h5>
                    <p>Combina criterios sin perder el contexto de la búsqueda.</p>
                </div>
                <span class="busqueda-filter-counter" id="busquedaFilterCounter">{{ $filtrosActivos }}</span>
            </div>

            <div class="accordion accordion-flush" id="accordionFiltros">

                {{-- FECHAS --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#filtroFechas">
                            <i class="fa-regular fa-calendar-days me-2"></i> Fechas
                        </button>
                    </h2>

                    <div id="filtroFechas" class="accordion-collapse collapse show" data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label">Filtrar por</label>
                                <select name="fecha_tipo" class="form-select">
                                    <option value="actualizacion" @selected(($filtros['fecha_tipo'] ?? 'actualizacion') === 'actualizacion')>Última modificación</option>
                                    <option value="creacion" @selected(($filtros['fecha_tipo'] ?? null) === 'creacion')>Fecha de creación</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Filtro rápido</label>
                                <select name="fecha_filtro" class="form-select" id="fechaFiltro">
                                    <option value="">Todos</option>
                                    <option value="hoy" @selected(($filtros['fecha_filtro'] ?? null) === 'hoy')>Hoy</option>
                                    <option value="7_dias" @selected(($filtros['fecha_filtro'] ?? null) === '7_dias')>Últimos 7 días</option>
                                    <option value="30_dias" @selected(($filtros['fecha_filtro'] ?? null) === '30_dias')>Últimos 30 días</option>
                                    <option value="este_mes" @selected(($filtros['fecha_filtro'] ?? null) === 'este_mes')>Este mes</option>
                                    <option value="mes_pasado" @selected(($filtros['fecha_filtro'] ?? null) === 'mes_pasado')>Mes pasado</option>
                                    <option value="personalizado" @selected(($filtros['fecha_filtro'] ?? null) === 'personalizado')>Rango personalizado</option>
                                </select>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">Desde</label>
                                    <input type="date" class="form-control" name="fecha_desde" value="{{ $filtros['fecha_desde'] ?? '' }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Hasta</label>
                                    <input type="date" class="form-control" name="fecha_hasta" value="{{ $filtros['fecha_hasta'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PERFIL --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filtroPerfil">
                            <i class="fa-regular fa-id-card me-2"></i> Información general
                        </button>
                    </h2>

                    <div id="filtroPerfil" class="accordion-collapse collapse" data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label">Sexo</label>
                                <select name="sexo" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($sexos as $sexo)
                                        <option value="{{ $sexo->id_sexo }}" @selected(($filtros['sexo'] ?? null) == $sexo->id_sexo)>
                                            {{ $sexo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">Edad mínima</label>
                                    <input type="number" min="18" max="100" class="form-control" name="edad_min" value="{{ $filtros['edad_min'] ?? '' }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Edad máxima</label>
                                    <input type="number" min="18" max="100" class="form-control" name="edad_max" value="{{ $filtros['edad_max'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- UBICACIÓN --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filtroUbicacion">
                            <i class="fa-solid fa-location-dot me-2"></i> Ubicación y disponibilidad
                        </button>
                    </h2>

                    <div id="filtroUbicacion" class="accordion-collapse collapse" data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label">País</label>
                                <select id="pais" name="pais" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($paises as $pais)
                                        <option value="{{ $pais->id_pais }}" @selected(($filtros['pais'] ?? null) == $pais->id_pais)>
                                            {{ $pais->nombre_pais }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Departamento</label>
                                <select id="departamento" name="departamento" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($departamentos as $departamento)
                                        <option value="{{ $departamento->id_departamento }}" @selected(($filtros['departamento'] ?? null) == $departamento->id_departamento)>
                                            {{ $departamento->nombre_departamento }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Municipio</label>
                                <select id="municipio_mh" name="municipio_mh" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($municipiosMh as $municipio)
                                        <option value="{{ $municipio->id_municipio_mh }}" @selected(($filtros['municipio_mh'] ?? null) == $municipio->id_municipio_mh)>
                                            {{ $municipio->municipio_mh_nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Distrito</label>
                                <select id="distrito" name="distrito" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($distritos as $distrito)
                                        <option
                                            value="{{ $distrito->id_municipio }}"
                                            data-pais="{{ $distrito->id_pais }}"
                                            data-departamento="{{ $distrito->id_departamento }}"
                                            data-municipio-mh="{{ $distrito->id_municipio_mh }}"
                                            @selected(($filtros['distrito'] ?? null) == $distrito->id_municipio)
                                        >
                                            {{ $distrito->nombre_distrito }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Disponibilidad</label>
                                <select name="disponibilidad" class="form-select">
                                    <option value="">Todas</option>
                                    @foreach($tiposDisponibilidad as $tipo)
                                        <option value="{{ $tipo->id_tipo_disponibilidad }}" @selected(($filtros['disponibilidad'] ?? null) == $tipo->id_tipo_disponibilidad)>
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ÁREAS Y HABILIDADES --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filtroHabilidades">
                            Áreas y habilidades técnicas
                        </button>
                    </h2>

                    <div id="filtroHabilidades" class="accordion-collapse collapse" data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">
                            @include('fac.consultores.busqueda-avanzada.partials._checkbox_habilidades', [
                                'titulo' => 'Áreas de especialización',
                                'nombre' => 'area_especializacion',
                                'items' => $areasEspecializacion,
                            ])

                            @include('fac.consultores.busqueda-avanzada.partials._checkbox_habilidades', [
                                'titulo' => 'Habilidades técnicas',
                                'nombre' => 'habilidades_tecnicas',
                                'items' => $habilidadesTecnicas,
                            ])
                        </div>
                    </div>
                </div>

                {{-- EDUCACIÓN --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filtroEducacion">
                            <i class="fa-solid fa-graduation-cap me-2"></i> Educación y atestados
                        </button>
                    </h2>

                    <div id="filtroEducacion" class="accordion-collapse collapse" data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">

                            <div class="alert alert-light border small mb-3">
                                Filtra consultores según los atestados registrados en su trayectoria educativa.
                            </div>

                            <div class="busqueda-checkbox-group mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">Nivel académico</label>
                                    <span class="badge badge-primary-soft">{{ count($nivelesAcademicos ?? []) }}</span>
                                </div>

                                <div class="busqueda-checkbox-scroll">
                                    @foreach($nivelesAcademicos ?? [] as $nivel)
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="nivel_academico[]"
                                                value="{{ $nivel->id_nivel_academico }}"
                                                id="nivel{{ $nivel->id_nivel_academico }}"
                                                @checked(in_array($nivel->id_nivel_academico, $filtros['nivel_academico'] ?? []))
                                            >
                                            <label class="form-check-label" for="nivel{{ $nivel->id_nivel_academico }}">
                                                {{ $nivel->nombre }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="busqueda-checkbox-group mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">Tipo de formación</label>
                                    <span class="badge badge-primary-soft">{{ count($tiposFormacion ?? []) }}</span>
                                </div>

                                <div class="busqueda-checkbox-scroll">
                                    @foreach($tiposFormacion ?? [] as $tipoFormacion)
                                        <div class="form-check">
                                            <input
                                                class="form-check-input js-tipo-formacion-filtro"
                                                type="checkbox"
                                                name="tipo_formacion[]"
                                                value="{{ $tipoFormacion->id_tipo_formacion }}"
                                                id="tipoFormacion{{ $tipoFormacion->id_tipo_formacion }}"
                                                @checked(in_array($tipoFormacion->id_tipo_formacion, $filtros['tipo_formacion'] ?? []))
                                            >
                                            <label class="form-check-label" for="tipoFormacion{{ $tipoFormacion->id_tipo_formacion }}">
                                                {{ $tipoFormacion->nombre }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="busqueda-checkbox-group mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">Tipo de atestado</label>
                                    <span class="badge badge-primary-soft" id="contadorTiposAtestado">{{ $tiposAtestado->count() }}</span>
                                </div>

                                <div class="alert alert-light border small py-2 mb-2" id="ayudaTipoAtestado">
                                    Selecciona un tipo de formación para ver únicamente los atestados relacionados.
                                </div>

                                <div class="busqueda-checkbox-scroll" id="contenedorTiposAtestado">
                                    @foreach($tiposAtestado ?? [] as $atestado)
                                        <div class="form-check" data-id-tipo-formacion="{{ $atestado->id_tipo_formacion }}">
                                            <input
                                                class="form-check-input js-tipo-atestado-filtro"
                                                type="checkbox"
                                                name="tipo_atestado[]"
                                                value="{{ $atestado->id_tipo_atestado }}"
                                                id="atestado{{ $atestado->id_tipo_atestado }}"
                                                @checked(in_array($atestado->id_tipo_atestado, $filtros['tipo_atestado'] ?? []))
                                            >
                                            <label class="form-check-label" for="atestado{{ $atestado->id_tipo_atestado }}">
                                                {{ $atestado->nombre }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Institución / entidad</label>
                                <input
                                    type="text"
                                    name="educacion_institucion"
                                    class="form-control"
                                    value="{{ $filtros['educacion_institucion'] ?? '' }}"
                                    placeholder="Ej. FEPADE, Universidad, Instituto..."
                                >
                            </div>

                            <div class="mb-0">
                                <label class="form-label">País del atestado</label>
                                <select name="educacion_pais" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($paises ?? [] as $pais)
                                        <option
                                            value="{{ $pais->id_pais }}"
                                            @selected(($filtros['educacion_pais'] ?? '') == $pais->id_pais)
                                        >
                                            {{ $pais->nombre_pais }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- IDIOMAS --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filtroIdiomas">
                            <i class="fa-solid fa-language me-2"></i> Idiomas
                        </button>
                    </h2>

                    <div id="filtroIdiomas" class="accordion-collapse collapse" data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">
                            @php
                                $idiomasSeleccionados = (array) request()->input('idiomas', []);
                            @endphp

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Idiomas</label>
                                <span class="badge badge-primary-soft">{{ count($idiomas ?? []) }}</span>
                            </div>

                            <div class="busqueda-checkbox-scroll" style="max-height: 350px;">
                                @foreach($idiomas ?? [] as $idioma)
                                    <div class="mb-2">
                                        <div class="form-check">
                                            <input
                                                type="checkbox"
                                                class="form-check-input idioma-check"
                                                id="idioma{{ $idioma->id_idioma }}"
                                                data-target="nivel-container-{{ $idioma->id_idioma }}"
                                                @checked(isset($idiomasSeleccionados[$idioma->id_idioma]))
                                            >
                                            <label class="form-check-label" for="idioma{{ $idioma->id_idioma }}">
                                                {{ $idioma->nombre }}
                                            </label>
                                        </div>

                                        <div
                                            id="nivel-container-{{ $idioma->id_idioma }}"
                                            class="ms-4 mt-2"
                                            style="display: {{ isset($idiomasSeleccionados[$idioma->id_idioma]) ? 'block' : 'none' }};"
                                        >
                                            <label class="form-label small">Nivel</label>
                                            <select class="form-select form-select-sm" name="idiomas[{{ $idioma->id_idioma }}]">
                                                <option value="">Seleccione nivel</option>
                                                @foreach($nivelesIdioma as $nivel)
                                                    <option
                                                        value="{{ $nivel->id_idioma_nivel }}"
                                                        @selected(($idiomasSeleccionados[$idioma->id_idioma] ?? null) == $nivel->id_idioma_nivel)
                                                    >
                                                        {{ $nivel->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- EXPERIENCIA --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filtroExperiencia">
                            <i class="fa-solid fa-briefcase me-2"></i> Experiencia profesional
                        </button>
                    </h2>

                    <div id="filtroExperiencia" class="accordion-collapse collapse" data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label">Cargo</label>
                                <input type="text" class="form-control" name="cargo" value="{{ $filtros['cargo'] ?? '' }}" placeholder="Ej. Facilitador, consultor, gerente...">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Empresa</label>
                                <input type="text" class="form-control" name="empresa" value="{{ $filtros['empresa'] ?? '' }}" placeholder="Nombre de empresa o institución">
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Años mínimos de experiencia</label>
                                <input type="number" min="0" max="60" class="form-control" name="anios_experiencia" value="{{ $filtros['anios_experiencia'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button class="btn btn-fepade" type="submit">
                    <i class="fas fa-filter me-1"></i> Aplicar ahora
                </button>

                <a href="{{ route('fac.busqueda.index') }}" class="btn btn-outline-secondary">
                    Limpiar
                </a>
            </div>
        </aside>

        <div id="resultadosConsultores">
            @include('fac.consultores.busqueda-avanzada.partials._resultados')
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formBusquedaAvanzada');
    const resultados = document.getElementById('resultadosConsultores');
    const pais = document.getElementById('pais');
    const departamento = document.getElementById('departamento');
    const municipioMh = document.getElementById('municipio_mh');
    const distrito = document.getElementById('distrito');

    const departamentoOriginalOptions = departamento ? departamento.innerHTML : '';
    const municipioMhOriginalOptions = municipioMh ? municipioMh.innerHTML : '';
    const distritoOriginalOptions = distrito ? distrito.innerHTML : '';

    let filtroTimer = null;
    let controller = null;
    let cargandoUbicacion = false;

    const rutas = {
        busqueda: @json(route('fac.busqueda.index')),
        departamentos: @json(route('fac.ajax.departamentos')),
        municipios: @json(route('fac.ajax.municipios')),
        distritos: @json(route('fac.ajax.distritos')),
        ubicacion: @json(route('fac.ajax.ubicacion.distrito')),
    };

    const resumenUi = {
        total: document.getElementById('busquedaTotalResultados'),
        totalPlural: document.getElementById('busquedaTotalPlural'),
        filtros: document.getElementById('busquedaFiltrosActivos'),
        filtrosPlural: document.getElementById('busquedaFiltrosPlural'),
        filtrosActivosPlural: document.getElementById('busquedaFiltrosActivosPlural'),
        contadorPanel: document.getElementById('busquedaFilterCounter'),
        busquedaIndicador: document.getElementById('busquedaGeneralIndicador'),
        busquedaTexto: document.getElementById('busquedaGeneralTexto'),
        chips: document.getElementById('busquedaActiveChips'),
    };

    const nombresFiltros = {
        fecha_filtro: 'Fecha',
        fecha_desde: 'Desde',
        fecha_hasta: 'Hasta',
        sexo: 'Sexo',
        edad_min: 'Edad mínima',
        edad_max: 'Edad máxima',
        pais: 'País',
        departamento: 'Departamento',
        municipio_mh: 'Municipio',
        distrito: 'Distrito',
        disponibilidad: 'Disponibilidad',
        area_especializacion: 'Área',
        habilidades_tecnicas: 'Habilidad',
        nivel_academico: 'Nivel académico',
        tipo_formacion: 'Tipo de formación',
        tipo_atestado: 'Tipo de atestado',
        educacion_institucion: 'Institución',
        educacion_pais: 'País formación',
        idiomas: 'Idioma',
        cargo: 'Cargo',
        empresa: 'Empresa',
        anios_experiencia: 'Años de experiencia',
    };

    const camposIgnoradosResumen = new Set(['page', 's', '_token', 'q', 'fecha_tipo']);

    function nombreBaseCampo(name) {
        return name.replace(/\[\]$/, '').replace(/\[[^\]]+\]$/, '');
    }

    function textoCampo(element) {
        if (element.tagName === 'SELECT') {
            const option = element.options[element.selectedIndex];
            return option ? option.textContent.trim() : element.value;
        }

        if (element.type === 'checkbox' || element.type === 'radio') {
            if (!element.checked) return '';
            const label = form.querySelector(`label[for="${element.id}"]`);
            return label ? label.textContent.trim() : element.value;
        }

        return element.value.trim();
    }

    function obtenerFiltrosActivosDesdeFormulario() {
        if (!form) return [];

        const filtros = [];
        const usados = new Set();

        form.querySelectorAll('input[name], select[name], textarea[name]').forEach(element => {
            const base = nombreBaseCampo(element.name);
            if (camposIgnoradosResumen.has(base)) return;

            const valor = textoCampo(element);
            if (!valor) return;
            if (['Todos', 'Todas', 'Seleccione nivel'].includes(valor)) return;

            const key = `${element.name}:${element.value}`;
            if (usados.has(key)) return;
            usados.add(key);

            filtros.push({
                label: nombresFiltros[base] ?? base,
                value: valor,
            });
        });

        return filtros;
    }

    function actualizarResumenBusqueda(total = null) {
        const filtros = obtenerFiltrosActivosDesdeFormulario();
        const cantidad = filtros.length;
        const busqueda = (form?.querySelector('[name="q"]')?.value ?? '').trim();

        if (resumenUi.filtros) resumenUi.filtros.textContent = cantidad;
        if (resumenUi.contadorPanel) resumenUi.contadorPanel.textContent = cantidad;
        if (resumenUi.filtrosPlural) resumenUi.filtrosPlural.textContent = cantidad === 1 ? '' : 's';
        if (resumenUi.filtrosActivosPlural) resumenUi.filtrosActivosPlural.textContent = cantidad === 1 ? '' : 's';

        if (resumenUi.busquedaIndicador) resumenUi.busquedaIndicador.textContent = busqueda ? 'Sí' : 'No';
        if (resumenUi.busquedaTexto) resumenUi.busquedaTexto.textContent = busqueda ? `Búsqueda: ${busqueda}` : 'Sin búsqueda general';

        if (Number.isInteger(total)) {
            if (resumenUi.total) resumenUi.total.textContent = total;
            if (resumenUi.totalPlural) resumenUi.totalPlural.textContent = total === 1 ? '' : 's';
        }

        if (!resumenUi.chips) return;

        resumenUi.chips.innerHTML = '';

        if (!cantidad) {
            const empty = document.createElement('span');
            empty.className = 'busqueda-active-empty';
            empty.textContent = 'Sin filtros avanzados activos.';
            resumenUi.chips.appendChild(empty);
            return;
        }

        filtros.forEach(filtro => {
            const chip = document.createElement('span');
            chip.className = 'busqueda-active-chip';
            chip.innerHTML = `<strong>${filtro.label}:</strong> ${filtro.value}`;
            resumenUi.chips.appendChild(chip);
        });
    }

    function resetSelect(select, label = 'Todos') {
        if (!select) return;
        select.innerHTML = `<option value="">${label}</option>`;
    }

    function restaurarUbicacionCompleta() {
        if (departamento && !pais.value) {
            departamento.innerHTML = departamentoOriginalOptions;
        }

        if (municipioMh && !departamento.value) {
            municipioMh.innerHTML = municipioMhOriginalOptions;
        }

        if (distrito && !pais.value && !departamento.value && !municipioMh.value) {
            distrito.innerHTML = distritoOriginalOptions;
        }
    }

    function appendOption(select, value, text, data = {}) {
        if (!select) return;

        const option = document.createElement('option');
        option.value = value;
        option.textContent = text;

        Object.entries(data).forEach(([key, val]) => {
            option.dataset[key] = val ?? '';
        });

        select.appendChild(option);
    }

    function setLoading(isLoading) {
        if (!resultados) return;
        resultados.classList.toggle('busqueda-resultados-loading', isLoading);
    }

    function buscar(pageUrl = null, delay = 350) {
        clearTimeout(filtroTimer);

        filtroTimer = setTimeout(() => {
            if (!form || !resultados) return;

            if (controller) controller.abort();
            controller = new AbortController();

            const formData = new FormData(form);
            const params = new URLSearchParams();

            for (const [key, value] of formData.entries()) {
                if (value !== '') params.append(key, value);
            }

            const url = pageUrl ?? `${rutas.busqueda}?${params.toString()}`;

            setLoading(true);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                signal: controller.signal,
            })
                .then(response => response.json())
                .then(data => {
                    resultados.innerHTML = data.html;

                    const totalActualizado = parseInt(resultados.querySelector('[data-total-resultados]')?.dataset.totalResultados ?? '', 10);
                    actualizarResumenBusqueda(Number.isInteger(totalActualizado) ? totalActualizado : null);

                    if (data.url) {
                        window.history.replaceState({}, '', data.url);
                    }
                })
                .catch(error => {
                    if (error.name !== 'AbortError') {
                        console.error('Error al actualizar búsqueda avanzada:', error);
                    }
                })
                .finally(() => setLoading(false));
        }, delay);
    }

    actualizarResumenBusqueda({{ (int) $totalResultados }});

    pais?.addEventListener('change', function () {
        resetSelect(departamento);
        resetSelect(municipioMh);
        resetSelect(distrito);

        if (!this.value) {
            restaurarUbicacionCompleta();
            buscar();
            return;
        }

        cargandoUbicacion = true;

        fetch(`${rutas.departamentos}?id_pais=${this.value}`)
            .then(response => response.json())
            .then(data => data.forEach(item => appendOption(departamento, item.id_departamento, item.nombre_departamento)))
            .finally(() => {
                cargandoUbicacion = false;
                buscar();
            });
    });

    departamento?.addEventListener('change', function () {
        resetSelect(municipioMh);
        resetSelect(distrito);

        if (!this.value) {
            if (municipioMh) {
                municipioMh.innerHTML = municipioMhOriginalOptions;
            }

            if (distrito) {
                distrito.innerHTML = distritoOriginalOptions;
            }

            buscar();
            return;
        }

        cargandoUbicacion = true;

        fetch(`${rutas.municipios}?id_departamento=${this.value}`)
            .then(response => response.json())
            .then(data => data.forEach(item => appendOption(municipioMh, item.id_municipio_mh, item.municipio_mh_nombre)))
            .finally(() => {
                cargandoUbicacion = false;
                buscar();
            });
    });

    municipioMh?.addEventListener('change', function () {
        resetSelect(distrito);

        if (!this.value) {
            if (distrito) {
                distrito.innerHTML = distritoOriginalOptions;
            }

            buscar();
            return;
        }

        cargandoUbicacion = true;

        fetch(`${rutas.distritos}?id_municipio_mh=${this.value}`)
            .then(response => response.json())
            .then(data => data.forEach(item => appendOption(distrito, item.id_municipio, item.nombre_distrito, {
                pais: item.id_pais,
                departamento: item.id_departamento,
                municipioMh: item.id_municipio_mh,
            })))
            .finally(() => {
                cargandoUbicacion = false;
                buscar();
            });
    });

    distrito?.addEventListener('change', function () {
        if (!this.value) {
            buscar();
            return;
        }

        const option = distrito.options[distrito.selectedIndex];

        if (option.dataset.pais) {
            pais.value = option.dataset.pais;
            departamento.value = option.dataset.departamento;
            municipioMh.value = option.dataset.municipioMh;
            buscar();
            return;
        }

        cargandoUbicacion = true;

        fetch(`${rutas.ubicacion}?id_municipio=${this.value}`)
            .then(response => response.json())
            .then(data => {
                pais.value = data.pais;
                departamento.value = data.departamento;
                municipioMh.value = data.municipio_mh;
            })
            .finally(() => {
                cargandoUbicacion = false;
                buscar();
            });
    });

    form?.addEventListener('submit', function (event) {
        event.preventDefault();
        actualizarResumenBusqueda();
        buscar(null, 0);
    });

    form?.querySelectorAll('input, select').forEach(element => {
        if (['pais', 'departamento', 'municipio_mh', 'distrito'].includes(element.id)) return;

        const eventName = ['text', 'number', 'date', 'search'].includes(element.type) ? 'input' : 'change';

        element.addEventListener(eventName, function () {
            actualizarResumenBusqueda();
            if (!cargandoUbicacion) buscar();
        });
    });

    resultados?.addEventListener('click', function (event) {
        const link = event.target.closest('.pagination a');

        if (!link) return;

        event.preventDefault();
        buscar(link.href, 0);
    });

    document.querySelectorAll('.idioma-check').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const target = document.getElementById(this.dataset.target);

            if (!target) return;

            if (this.checked) {
                target.style.display = 'block';
            } else {
                target.style.display = 'none';

                const select = target.querySelector('select');
                if (select) select.value = '';
            }

            actualizarResumenBusqueda();
        });
    });

    /* =====================================================
    CASCADA TIPO FORMACIÓN -> TIPO ATESTADO
    ===================================================== */

    const checksTipoFormacion = document.querySelectorAll('.js-tipo-formacion-filtro');
    const contenedorTiposAtestado = document.getElementById('contenedorTiposAtestado');
    const contadorTiposAtestado = document.getElementById('contadorTiposAtestado');
    const ayudaTipoAtestado = document.getElementById('ayudaTipoAtestado');

    if (checksTipoFormacion.length && contenedorTiposAtestado) {

        function obtenerTiposFormacionSeleccionados() {
            return Array.from(checksTipoFormacion)
                .filter(c => c.checked)
                .map(c => c.value);
        }

        function filtrarTiposAtestado() {

            const seleccionados = obtenerTiposFormacionSeleccionados();
            const mostrarTodos = seleccionados.length === 0;

            let visibles = 0;

            contenedorTiposAtestado
                .querySelectorAll('.form-check[data-id-tipo-formacion]')
                .forEach(function (item) {

                    const idTipo = item.dataset.idTipoFormacion;
                    const check = item.querySelector('input');

                    const mostrar = mostrarTodos || seleccionados.includes(idTipo);

                    item.classList.toggle('d-none', !mostrar);

                    if (!mostrar && check) {
                        check.checked = false;
                    }

                    if (mostrar) {
                        visibles++;
                    }

                });

            contadorTiposAtestado.textContent = visibles;

            ayudaTipoAtestado.textContent = mostrarTodos
                ? 'Selecciona un tipo de formación para filtrar los tipos de atestado.'
                : 'Mostrando únicamente los tipos de atestado relacionados.';
        }

        checksTipoFormacion.forEach(function (check) {
            check.addEventListener('change', filtrarTiposAtestado);
        });

        filtrarTiposAtestado();
    }

});
</script>
@endpush