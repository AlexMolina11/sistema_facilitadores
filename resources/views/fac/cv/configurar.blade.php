@extends('layouts.app')

@section('title', 'Constructor de CV | Facilitadores FEPADE')
@section('page-title', 'Constructor de CV')
@section('page-subtitle', 'Seleccione plantilla y elementos específicos para exportar.')

@section('content')

<x-ui.page-header 
    title="Constructor de CV" 
    subtitle="{{ $consultor->nombre_completo }}"
>
    <a href="{{ route('fac.busqueda.index') }}" class="btn btn-outline-secondary">
        Volver a búsqueda
    </a>
</x-ui.page-header>

<form id="cvPdfForm" method="POST" action="{{ route('fac.cv.pdf', $consultor) }}" target="_blank">
    @csrf
    <input type="hidden" name="config_json" id="config_json">
</form>

<div class="cv-builder" id="cvBuilder">
    <div class="cv-preview-panel">
        <div class="cv-preview-toolbar">
            <div>
                <strong>Vista previa</strong>
                <span id="previewTemplateName">Formato CV FEPADE</span>
            </div>

            <button type="button" class="btn btn-fepade" id="btnGenerarPdf">
                <i class="fa-solid fa-file-pdf me-1"></i> Generar PDF
            </button>
        </div>

        <div class="cv-paper-wrap" id="cvPreviewScroll">
            <div class="cv-paper" id="cvPreview"></div>
        </div>
    </div>

    <aside class="cv-config-panel">
        <div class="cv-config-sticky">
            <div class="cv-config-toolbar">
            <div class="fepade-card mb-3">
                <h5 class="mb-2">1. Plantilla</h5>

                <select class="form-select" id="plantillaSelect">
                    @foreach($plantillas as $key => $nombre)
                        <option value="{{ $key }}">{{ $nombre }}</option>
                    @endforeach
                </select>

                <small class="text-muted d-block mt-2">
                    Puede cambiar de plantilla sin perder los elementos seleccionados.
                </small>

                <div class="d-grid gap-2 mt-3">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectAllCv">Seleccionar todo</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnClearAllCv">Quitar todo</button>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnExpandAllCv">Expandir todo</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCollapseAllCv">Contraer todo</button>
                    </div>
                </div>
            </div>

            <div class="fepade-card mb-3 cv-filter-card">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <div>
                        <h5 class="mb-1">2. Filtrar trayectoria</h5>
                        <small class="text-muted">Experiencia, formación, capacitaciones, áreas y habilidades.</small>
                    </div>
                    <span class="badge text-bg-light" id="atestadosFilterStatus">Todos</span>
                </div>

                <div class="btn-group w-100 cv-period-filter" role="group" aria-label="Filtrar trayectoria por período">
                    <button type="button" class="btn btn-sm btn-outline-secondary active" data-years="all">Todos</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-years="2">2 años</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-years="5">5 años</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-years="10">10 años</button>
                </div>

                <small class="text-muted d-block mt-2" id="atestadosFilterSummary">
                    Se muestra toda la trayectoria registrada.
                </small>
            </div>

            <div class="fepade-card mb-3 cv-template-manual-fields" id="manualFieldsCard" hidden>
                <h5 class="mb-1">3. Información completada por FEPADE</h5>
                <small class="text-muted d-block mb-3">
                    Estos datos corresponden a la oferta o consultoría actual y se reflejarán en la vista previa y en el PDF.
                </small>

                <div class="cv-manual-field" data-template="mineducyt_birf" hidden>
                    <div class="mb-3">
                        <label for="manualCargoNumero" class="form-label fw-semibold">Nombre del cargo y número</label>
                        <input id="manualCargoNumero" class="form-control cv-manual-input" type="text"
                            data-manual-field="cargo_numero"
                            placeholder="Ej.: Especialista en formación – EXP-02">
                    </div>

                    <div class="mb-3">
                        <label for="manualNombreConsultoriaMineducyt" class="form-label fw-semibold">Nombre de la consultoría o proceso</label>
                        <input id="manualNombreConsultoriaMineducyt" class="form-control cv-manual-input" type="text"
                            data-manual-field="nombre_consultoria"
                            placeholder="Digite el nombre de la consultoría o proceso...">
                    </div>

                    <div>
                        <label for="manualTareasAsignadas" class="form-label fw-semibold">
                            Tareas detalladas asignadas al grupo de Expertos del Consultor
                        </label>
                        <textarea id="manualTareasAsignadas" class="form-control cv-manual-input" rows="5"
                            data-manual-field="tareas_asignadas"
                            placeholder="Digite las tareas asignadas por FEPADE..."></textarea>
                    </div>
                </div>

                <div class="cv-manual-field" data-template="resumen_personal" hidden>
                    <div class="mb-3">
                        <label for="manualNombreConsultoriaResumen" class="form-label fw-semibold">Nombre de la consultoría o proceso</label>
                        <input id="manualNombreConsultoriaResumen" class="form-control cv-manual-input" type="text"
                            data-manual-field="nombre_consultoria"
                            placeholder="Digite el nombre de la consultoría o proceso...">
                    </div>

                    <div class="mb-3">
                        <label for="manualCargoPropuesto" class="form-label fw-semibold">Cargo propuesto</label>
                        <input id="manualCargoPropuesto" class="form-control cv-manual-input" type="text"
                            data-manual-field="cargo_propuesto"
                            placeholder="Digite el cargo propuesto...">
                    </div>

                    <div>
                        <label for="manualActividadesConsultoria" class="form-label fw-semibold">
                            Detalle de las actividades asignadas en esta consultoría
                        </label>
                        <textarea id="manualActividadesConsultoria" class="form-control cv-manual-input" rows="5"
                            data-manual-field="actividades_consultoria"
                            placeholder="Digite el detalle de las actividades asignadas..."></textarea>
                    </div>
                </div>
            </div>

            </div>

            <div class="cv-config-sections">
            <div class="accordion" id="cvAccordion">

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#cvDatos">
                            Datos personales
                        </button>
                    </h2>
                    <div id="cvDatos" class="accordion-collapse collapse show" data-bs-parent="#cvAccordion">
                        <div class="accordion-body">
                            <div class="d-flex gap-2 mb-3">
                                <button type="button" class="btn btn-sm btn-outline-primary cv-section-select" data-section="personal">
                                    Seleccionar sección
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary cv-section-clear" data-section="personal">
                                    Quitar sección
                                </button>
                            </div>
                            @foreach($cvData['personal'] as $key => $value)
                                @continue($key === 'foto_pdf')

                                <div class="form-check cv-check">
                                    <input class="form-check-input cv-toggle" type="checkbox" checked
                                        data-section="personal" data-item="personal" data-field="{{ $key }}"
                                        id="personal_{{ $key }}">
                                    <label class="form-check-label" for="personal_{{ $key }}">
                                        {{ ucfirst(str_replace('_', ' ', $key)) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @include('fac.cv.partials.config-list', [
                    'titulo' => 'Correos',
                    'collapseId' => 'cvEmails',
                    'section' => 'emails',
                    'items' => $cvData['emails'],
                    'fields' => ['email' => 'Correo'],
                    'mainField' => 'email',
                ])

                @include('fac.cv.partials.config-list', [
                    'titulo' => 'Teléfonos',
                    'collapseId' => 'cvTelefonos',
                    'section' => 'telefonos',
                    'items' => $cvData['telefonos'],
                    'fields' => [
                        'tipo' => 'Tipo',
                        'extension' => 'Extensión',
                        'numero' => 'Número',
                    ],
                    'mainField' => 'numero',
                ])

                @include('fac.cv.partials.config-list', [
                    'titulo' => 'Experiencia laboral',
                    'collapseId' => 'cvExperiencia',
                    'section' => 'experiencias',
                    'items' => $cvData['experiencias'],
                    'fields' => [
                        'empresa' => 'Empresa',
                        'cargo' => 'Cargo',
                        'descripcion' => 'Descripción',
                        'desde' => 'Desde',
                        'hasta' => 'Hasta',
                        'jefe_nombre' => 'Jefe inmediato',
                        'jefe_email' => 'Correo jefe',
                        'jefe_telefono' => 'Teléfono jefe',
                    ],
                    'mainField' => 'cargo',
                ])

                @include('fac.cv.partials.config-list', [
                    'titulo' => 'Trayectoria académica y profesional',
                    'collapseId' => 'cvAtestados',
                    'section' => 'atestados',
                    'items' => $cvData['atestados'],
                    'fields' => [
                        'tipo_formacion' => 'Tipo formación',
                        'tipo_atestado' => 'Tipo atestado',
                        'nivel' => 'Nivel',
                        'titulo' => 'Título',
                        'institucion' => 'Institución',
                        'descripcion' => 'Descripción',
                        'pais' => 'País',
                        'fecha_inicio' => 'Fecha inicio',
                        'fecha_fin' => 'Fecha fin',
                        'fecha_emision' => 'Fecha emisión',
                        'fecha_vencimiento' => 'Fecha vencimiento',
                        'horas' => 'Horas',
                        'archivo_url' => 'Ver atestado',
                    ],
                    'mainField' => 'titulo',
                ])

                @include('fac.cv.partials.config-list', [
                    'titulo' => 'Capacitaciones FEPADE',
                    'collapseId' => 'cvFepade',
                    'section' => 'capacitaciones_fepade',
                    'items' => $cvData['capacitaciones_fepade'],
                    'fields' => [
                        'nombre_evento' => 'Nombre evento',
                        'tema' => 'Tema',
                        'institucion' => 'Institución',
                        'modalidad' => 'Modalidad',
                        'fecha_inicio' => 'Fecha inicio',
                        'fecha_fin' => 'Fecha fin',
                        'horas' => 'Horas',
                        'fuente' => 'Fuente',
                    ],
                    'mainField' => 'nombre_evento',
                ])

                @include('fac.cv.partials.config-list', [
                    'titulo' => 'Idiomas',
                    'collapseId' => 'cvIdiomas',
                    'section' => 'idiomas',
                    'items' => $cvData['idiomas'],
                    'fields' => [
                        'idioma' => 'Idioma',
                        'nivel' => 'Nivel',
                        'certificado_url' => 'Ver certificado',
                    ],
                    'mainField' => 'idioma',
                ])

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#cvReferencias">
                            Referencias
                        </button>
                    </h2>

                    <div id="cvReferencias" class="accordion-collapse collapse">
                        <div class="accordion-body">

                            <div class="cv-section-actions">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary cv-section-select"
                                        data-section="referencias">
                                    Seleccionar sección
                                </button>

                                <button type="button"
                                        class="btn btn-sm btn-outline-secondary cv-section-clear"
                                        data-section="referencias">
                                    Quitar sección
                                </button>
                            </div>

                            @forelse(collect($cvData['referencias'])->groupBy(fn($item) => $item['tipo'] ?: 'Sin tipo') as $tipo => $referencias)

                                <div class="cv-config-group-title">
                                    {{ $tipo }}
                                </div>

                                @foreach($referencias as $item)

                                    <div class="cv-config-item"
                                        data-config-section="referencias"
                                        data-config-item="{{ $item['id'] }}">

                                        <div class="cv-config-item-header">

                                            <strong>
                                                {{ $item['nombre'] ?? 'Referencia sin nombre' }}
                                            </strong>

                                            <div class="cv-config-item-actions">

                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary cv-item-select"
                                                        data-section="referencias"
                                                        data-item="{{ $item['id'] }}">
                                                    Todo
                                                </button>

                                                <button type="button"
                                                        class="btn btn-sm btn-outline-secondary cv-item-clear"
                                                        data-section="referencias"
                                                        data-item="{{ $item['id'] }}">
                                                    Nada
                                                </button>

                                            </div>

                                        </div>

                                        @foreach([
                                            'tipo'=>'Tipo',
                                            'nombre'=>'Nombre',
                                            'telefono'=>'Teléfono',
                                            'correo'=>'Correo',
                                            'empresa'=>'Empresa',
                                            'cargo'=>'Cargo',
                                        ] as $field=>$label)

                                            <div class="form-check cv-check">

                                                <input
                                                    class="form-check-input cv-toggle"
                                                    type="checkbox"
                                                    checked
                                                    data-section="referencias"
                                                    data-item="{{ $item['id'] }}"
                                                    data-field="{{ $field }}"
                                                    id="referencias_{{ $item['id'] }}_{{ $field }}"
                                                >

                                                <label class="form-check-label"
                                                    for="referencias_{{ $item['id'] }}_{{ $field }}">
                                                    {{ $label }}
                                                </label>

                                            </div>

                                        @endforeach

                                    </div>

                                @endforeach

                            @empty

                                <p class="text-muted mb-0">
                                    No hay referencias registradas.
                                </p>

                            @endforelse

                        </div>
                    </div>
                </div>

                @include('fac.cv.partials.config-list', [
                    'titulo' => 'Disponibilidad',
                    'collapseId' => 'cvDisponibilidad',
                    'section' => 'disponibilidades',
                    'items' => $cvData['disponibilidades'],
                    'fields' => ['nombre' => 'Disponibilidad'],
                    'mainField' => 'nombre',
                ])

                <div class="accordion-item cv-template-only" data-template="fepade">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#cvFepadeOpciones">
                            Opciones específicas
                        </button>
                    </h2>

                    <div id="cvFepadeOpciones" class="accordion-collapse collapse">
                        <div class="accordion-body">

                            <div class="cv-config-item">
                                <strong>Países con experiencia de trabajo últimos 10 años</strong>

                                <div class="form-check cv-check mt-2">
                                    <input
                                        class="form-check-input cv-toggle"
                                        type="checkbox"
                                        checked
                                        data-section="fepade_opciones"
                                        data-item="paises_experiencia_10"
                                        data-field="mostrar"
                                        id="fepade_paises_experiencia_10"
                                    >

                                    <label class="form-check-label" for="fepade_paises_experiencia_10">
                                        Mostrar países donde tiene experiencia de trabajo en los últimos 10 años
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="accordion-item">

                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#cvAreas">

                            Áreas y habilidades técnicas

                        </button>
                    </h2>

                    <div id="cvAreas" class="accordion-collapse collapse">

                        <div class="accordion-body">

                            <div class="cv-section-actions">

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary cv-section-select"
                                    data-section="areas">

                                    Seleccionar sección

                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary cv-section-clear"
                                    data-section="areas">

                                    Quitar sección

                                </button>

                            </div>

                            @forelse($cvData['areas'] as $area)

                                <div class="cv-config-item"
                                    data-config-section="areas"
                                    data-config-item="{{ $area['id'] }}">

                                    <div class="cv-config-item-header">

                                        <strong>

                                            {{ $area['nombre'] ?: 'Área sin nombre' }}

                                        </strong>

                                        <div class="cv-config-item-actions">

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary cv-area-select"
                                                data-area="{{ $area['id'] }}">

                                                Todo

                                            </button>

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-secondary cv-area-clear"
                                                data-area="{{ $area['id'] }}">

                                                Nada

                                            </button>

                                        </div>

                                    </div>

                                    <div class="form-check cv-check">

                                        <input
                                            class="form-check-input cv-toggle cv-area-toggle"
                                            type="checkbox"
                                            checked
                                            data-section="areas"
                                            data-item="{{ $area['id'] }}"
                                            data-field="nombre"
                                            id="area_{{ $area['id'] }}"
                                        >

                                        <label
                                            class="form-check-label"
                                            for="area_{{ $area['id'] }}">

                                            Mostrar área

                                        </label>

                                    </div>

                                    @if(count($area['habilidades']))

                                        <div class="cv-config-subtitle">
                                            Habilidades técnicas
                                        </div>

                                        @foreach($area['habilidades'] as $hab)

                                            <div class="form-check cv-check ms-3">

                                                <input
                                                    class="form-check-input cv-toggle cv-habilidad-toggle"
                                                    type="checkbox"
                                                    checked
                                                    data-section="habilidades_area_{{ $area['id'] }}"
                                                    data-item="{{ $hab['id'] }}"
                                                    data-field="nombre"
                                                    data-area="{{ $area['id'] }}"
                                                    id="hab_{{ $area['id'] }}_{{ $hab['id'] }}"
                                                >

                                                <label
                                                    class="form-check-label"
                                                    for="hab_{{ $area['id'] }}_{{ $hab['id'] }}">

                                                    {{ $hab['nombre'] }}

                                                </label>

                                            </div>

                                        @endforeach

                                    @endif

                                </div>

                            @empty

                                <p class="text-muted mb-0">

                                    No hay áreas registradas.

                                </p>

                            @endforelse

                        </div>

                    </div>

                </div>

            </div>
            </div>
        </div>
    </aside>
</div>

<script>
    window.cvData = @json($cvData);
    window.cvPlantillas = @json($plantillas);
    window.cvCatalogos = @json($cvCatalogos ?? []);
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const preview = document.getElementById('cvPreview');
    const plantillaSelect = document.getElementById('plantillaSelect');
    const templateName = document.getElementById('previewTemplateName');
    const configInput = document.getElementById('config_json');
    const pdfForm = document.getElementById('cvPdfForm');

    const state = {
        plantilla: plantillaSelect.value,
        selections: {},
        filtros: {
            atestados_anios: 'all'
        },
        campos_manual: {
            cargo_numero: '',
            nombre_consultoria: '',
            cargo_propuesto: '',
            tareas_asignadas: '',
            actividades_consultoria: ''
        }
    };

    function setToggle(input, checked) {
        input.checked = checked;

        const section = input.dataset.section;
        const item = input.dataset.item;
        const field = input.dataset.field;

        state.selections[section] ??= {};
        state.selections[section][item] ??= {};
        state.selections[section][item][field] = checked;
    }

    function setSection(section, checked) {
        document.querySelectorAll(`.cv-toggle[data-section="${section}"]`).forEach(input => {
            setToggle(input, checked);
        });

        if (section === 'areas') {
            document.querySelectorAll('.cv-habilidad-toggle').forEach(input => {
                setToggle(input, checked);
            });
        }
    }

    function setItem(section, item, checked) {
        document.querySelectorAll(`.cv-toggle[data-section="${section}"][data-item="${item}"]`).forEach(input => {
            setToggle(input, checked);
        });
    }

    function setArea(areaId, checked) {
        document.querySelectorAll(`.cv-toggle[data-section="areas"][data-item="${areaId}"]`).forEach(input => {
            setToggle(input, checked);
        });

        document.querySelectorAll(`.cv-habilidad-toggle[data-area="${areaId}"]`).forEach(input => {
            setToggle(input, checked);
        });
    }

    function syncAreaRules(areaId) {
        const areaSelected = isSelected('areas', areaId, 'nombre');

        document.querySelectorAll(`.cv-habilidad-toggle[data-area="${areaId}"]`).forEach(input => {
            if (!areaSelected) {
                setToggle(input, false);
            }
        });
    }

    function initSelections() {
        document.querySelectorAll('.cv-toggle').forEach(input => {
            const section = input.dataset.section;
            const item = input.dataset.item;
            const field = input.dataset.field;

            state.selections[section] ??= {};
            state.selections[section][item] ??= {};
            state.selections[section][item][field] = input.checked;
        });
    }

    function isSelected(section, item, field) {
        return state.selections?.[section]?.[item]?.[field] === true;
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) return '';
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function escapeMultiline(value) {
        return escapeHtml(value).replace(/\r?\n/g, '<br>');
    }

    function manualValue(field) {
        return escapeMultiline(state.campos_manual?.[field] || '');
    }

    function atestadoReferenceDate(item) {
        return parseDateIso(item.fecha_fin_iso)
            || parseDateIso(item.fecha_emision_iso)
            || parseDateIso(item.fecha_inicio_iso);
    }

    function capacitacionReferenceDate(item) {
        return parseDateIso(item.fecha_fin_iso)
            || parseDateIso(item.fecha_inicio_iso);
    }

    function experienciaIntersectsPeriod(item, limit, today) {
        const start = parseDateIso(item.desde_iso);
        const end = item.trabajo_actual_bool
            ? today
            : parseDateIso(item.hasta_iso);

        if (!end) return false;

        return end >= limit && (!start || start <= today);
    }

    function setConfigItemVisibility(section, itemId, visible) {
        const element = document.querySelector(`[data-config-section="${section}"][data-config-item="${itemId}"]`);

        if (element) {
            element.hidden = !visible;
            element.classList.toggle('is-period-filtered', !visible);
        }
    }

    function setWholeItem(section, itemId, checked) {
        document.querySelectorAll(`.cv-toggle[data-section="${section}"][data-item="${itemId}"]`).forEach(input => {
            setToggle(input, checked);
        });
    }

    function applyTrajectoryPeriodFilter(years) {
        const normalized = years === 'all' ? 'all' : Number(years);
        const today = new Date();
        const limit = new Date(today.getFullYear(), today.getMonth(), today.getDate());

        if (normalized !== 'all') {
            limit.setFullYear(limit.getFullYear() - normalized);
        }

        const visibleAtestados = new Set();
        const visibleCapacitaciones = new Set();
        const counts = {
            experiencias: 0,
            atestados: 0,
            capacitaciones: 0,
            areas: 0,
        };

        window.cvData.experiencias.forEach(item => {
            const visible = normalized === 'all'
                ? true
                : experienciaIntersectsPeriod(item, limit, today);

            setWholeItem('experiencias', item.id, visible);
            setConfigItemVisibility('experiencias', item.id, visible);
            if (visible) counts.experiencias++;
        });

        window.cvData.atestados.forEach(item => {
            const referenceDate = atestadoReferenceDate(item);
            const visible = normalized === 'all'
                ? true
                : Boolean(referenceDate && referenceDate >= limit && referenceDate <= today);

            setWholeItem('atestados', item.id, visible);
            setConfigItemVisibility('atestados', item.id, visible);

            if (visible) {
                visibleAtestados.add(Number(item.id));
                counts.atestados++;
            }
        });

        window.cvData.capacitaciones_fepade.forEach(item => {
            const referenceDate = capacitacionReferenceDate(item);
            const visible = normalized === 'all'
                ? true
                : Boolean(referenceDate && referenceDate >= limit && referenceDate <= today);

            setWholeItem('capacitaciones_fepade', item.id, visible);
            setConfigItemVisibility('capacitaciones_fepade', item.id, visible);

            if (visible) {
                visibleCapacitaciones.add(Number(item.id));
                counts.capacitaciones++;
            }
        });

        window.cvData.areas.forEach(area => {
            const atestadoId = area.id_atestado ? Number(area.id_atestado) : null;
            const capacitacionId = area.id_capacitacion_fepade ? Number(area.id_capacitacion_fepade) : null;

            const visible = normalized === 'all'
                ? true
                : Boolean(
                    (atestadoId && visibleAtestados.has(atestadoId))
                    || (capacitacionId && visibleCapacitaciones.has(capacitacionId))
                );

            setArea(area.id, visible);
            setConfigItemVisibility('areas', area.id, visible);
            if (visible) counts.areas++;
        });

        state.filtros.atestados_anios = normalized;
        updateTrajectoryFilterUi(counts);
        render();
    }

    function currentVisibleCount(section, items) {
        return items.filter(item => {
            return Boolean(document.querySelector(`.cv-toggle[data-section="${section}"][data-item="${item.id}"]:checked`));
        }).length;
    }

    function updateTrajectoryFilterUi(counts = null) {
        const years = state.filtros.atestados_anios;
        const actual = counts || {
            experiencias: currentVisibleCount('experiencias', window.cvData.experiencias),
            atestados: currentVisibleCount('atestados', window.cvData.atestados),
            capacitaciones: currentVisibleCount('capacitaciones_fepade', window.cvData.capacitaciones_fepade),
            areas: currentVisibleCount('areas', window.cvData.areas),
        };

        document.querySelectorAll('.cv-period-filter [data-years]').forEach(button => {
            button.classList.toggle('active', String(button.dataset.years) === String(years));
        });

        const status = document.getElementById('atestadosFilterStatus');
        const summary = document.getElementById('atestadosFilterSummary');

        status.textContent = years === 'all' ? 'Todos' : `Últimos ${years} años`;
        summary.textContent = `${actual.experiencias} experiencias, ${actual.atestados} atestados, ${actual.capacitaciones} capacitaciones y ${actual.areas} áreas visibles.`;
    }

    function renderManualFieldsVisibility() {
        const card = document.getElementById('manualFieldsCard');
        const supported = ['mineducyt_birf', 'resumen_personal'];
        const visible = supported.includes(state.plantilla);

        card.hidden = !visible;

        document.querySelectorAll('.cv-manual-field').forEach(field => {
            field.hidden = field.dataset.template !== state.plantilla;
        });

        document.querySelectorAll('.cv-manual-input').forEach(input => {
            const field = input.dataset.manualField;
            const stateValue = state.campos_manual?.[field] ?? '';

            if (input.value !== stateValue) {
                input.value = stateValue;
            }
        });
    }

    function visibleFields(section, item, fields) {
        return fields
            .filter(field => isSelected(section, item.id ?? 'personal', field) && item[field])
            .map(field => escapeHtml(item[field]));
    }

    function personal(field) {
        return isSelected('personal', 'personal', field)
            ? escapeHtml(window.cvData.personal[field])
            : '';
    }

    function rowIsVisible(section, item, fields) {
        return fields.some(field => isSelected(section, item.id, field) && item[field]);
    }

    function renderRows(section, items, fields) {
        return items
            .filter(item => rowIsVisible(section, item, fields))
            .map(item => {
                return `<tr>${fields.map(field => {
                    if (!isSelected(section, item.id, field) || !item[field]) {
                        return `<td>&nbsp;</td>`;
                    }

                    if (field === 'archivo_url') {
                        return `<td><a href="${escapeHtml(item[field])}" target="_blank">Ver atestado</a></td>`;
                    }

                    if (field === 'certificado_url') {
                        return `<td><a href="${escapeHtml(item[field])}" target="_blank">Ver certificado</a></td>`;
                    }

                    return `<td>${escapeHtml(item[field])}</td>`;
                }).join('')}</tr>`;
            }).join('');
    }

    function sectionTitle(title) {
        return `<h2>${title}</h2>`;
    }

    const TIPO_FORMACION = window.cvCatalogos.tipo_formacion || {};

    function parseDateIso(value) {
        if (!value) return null;

        const date = new Date(value + 'T00:00:00');

        return isNaN(date.getTime()) ? null : date;
    }

    function atestadosPorTipo(ids) {
        return window.cvData.atestados.filter(item => {
            return ids.includes(Number(item.id_tipo_formacion))
                && rowIsVisible('atestados', item, [
                    'titulo',
                    'institucion',
                    'fecha_inicio',
                    'fecha_fin',
                    'archivo_url'
                ]);
        });
    }

    function cargoFepade() {
        const experiencias = [...window.cvData.experiencias];

        const actual = experiencias.find(item => item.trabajo_actual_bool === true || item.hasta === 'Actualidad');

        if (actual && isSelected('experiencias', actual.id, 'cargo')) {
            return escapeHtml(actual.cargo);
        }

        const reciente = experiencias
            .filter(item => item.hasta_iso)
            .sort((a, b) => {
                const fechaA = parseDateIso(a.hasta_iso);
                const fechaB = parseDateIso(b.hasta_iso);

                return (fechaB?.getTime() || 0) - (fechaA?.getTime() || 0);
            })[0];

        return reciente && isSelected('experiencias', reciente.id, 'cargo')
            ? escapeHtml(reciente.cargo)
            : '';
    }

    function paisesExperienciaUltimos10() {
        const hoy = new Date();
        const limite = new Date();
        limite.setFullYear(hoy.getFullYear() - 10);

        const paises = window.cvData.experiencias
            .filter(item => {
                const hasta = item.trabajo_actual_bool
                    ? hoy
                    : parseDateIso(item.hasta_iso);

                return hasta && hasta >= limite && item.pais;
            })
            .map(item => item.pais);

        return [...new Set(paises)];
    }

    function asociacionesFepade() {
        return window.cvData.areas
            .filter(area => isSelected('areas', area.id, 'nombre'))
            .map(area => {
                const atestado = window.cvData.atestados.find(a => Number(a.id) === Number(area.id_atestado));
                const institucion = atestado?.institucion || '';

                const habilidades = [];

                document.querySelectorAll(`.cv-habilidad-toggle[data-area="${area.id}"]`).forEach(input => {
                    const habId = input.dataset.item;

                    if (!isSelected(`habilidades_area_${area.id}`, habId, 'nombre')) {
                        return;
                    }

                    const label = input.closest('.form-check')?.querySelector('label');
                    const nombre = label ? label.textContent.trim() : '';

                    if (nombre) {
                        habilidades.push(nombre);
                    }
                });

                return {
                    institucion,
                    area: area.nombre,
                    habilidades: [...new Set(habilidades)]
                };
            })
            .filter(item => item.institucion || item.area || item.habilidades.length);
    }

    function renderTemplateVisibility() {
        document.querySelectorAll('.cv-template-only').forEach(el => {
            el.style.display = el.dataset.template === state.plantilla ? '' : 'none';
        });
    }

    function renderFepade() {
        const data = window.cvData;

        const educacionFormal = atestadosPorTipo(TIPO_FORMACION.educacion_formal || []);

        const otrosEstudios = atestadosPorTipo([
            ...(TIPO_FORMACION.acreditacion || []),
            ...(TIPO_FORMACION.educacion_continua || []),
        ]);

        const consultorias = atestadosPorTipo([
            ...(TIPO_FORMACION.capacitacion_impartida || []),
            ...(TIPO_FORMACION.capacitacion_recibida || []),
            ...(TIPO_FORMACION.consultoria_realizada || []),
        ]);

        const asociaciones = asociacionesFepade();

        const mostrarPaisesExperiencia = isSelected('fepade_opciones', 'paises_experiencia_10', 'mostrar');
        const paisesExperiencia = paisesExperienciaUltimos10();

        let html = `
            <div class="cv-fepade-document">
                <div class="cv-fepade-hero">
                    <div class="cv-fepade-brand">CONSULTORES FEPADE 2026</div>
                    <h1>Hoja de Vida</h1>
                    <p>Formato CV FEPADE</p>
                </div>
        `;

        html += `
            <table class="cv-table cv-table-clean">
                <tr><th>Cargo:</th><td>${cargoFepade() || '&nbsp;'}</td></tr>
                <tr><th>Nombre del Profesional:</th><td>${personal('nombre')}</td></tr>
                <tr><th>Fecha de nacimiento:</th><td>${personal('fecha_nacimiento')}</td></tr>
                <tr><th>País de ciudadanía/residencia:</th><td>${personal('residencia')}</td></tr>
            </table>
        `;

        html += sectionTitle('Educación:');
        html += `
            <table class="cv-table">
                <thead>
                    <tr>
                        <th>Título obtenido</th>
                        <th>Institución</th>
                        <th>Fecha de estudios</th>
                    </tr>
                </thead>
                <tbody>
                    ${educacionFormal.map(item => `
                        <tr>
                            <td>${isSelected('atestados', item.id, 'titulo') ? escapeHtml(item.titulo) : ''}</td>
                            <td>${isSelected('atestados', item.id, 'institucion') ? escapeHtml(item.institucion) : ''}</td>
                            <td>${isSelected('atestados', item.id, 'fecha_fin') ? escapeHtml(item.fecha_fin) : ''}</td>
                        </tr>
                    `).join('') || '<tr><td colspan="3">&nbsp;</td></tr>'}
                </tbody>
            </table>
        `;

        html += sectionTitle('Asociaciones profesionales a las que pertenece:');
        html += `
            <table class="cv-table">
                <thead>
                    <tr>
                        <th>Institución</th>
                        <th>Área de especialización</th>
                        <th>Habilidad técnica</th>
                    </tr>
                </thead>
                <tbody>
                    ${asociaciones.map(item => `
                        <tr>
                            <td>${escapeHtml(item.institucion) || '&nbsp;'}</td>
                            <td>${escapeHtml(item.area) || '&nbsp;'}</td>
                            <td>${item.habilidades.length ? item.habilidades.map(h => escapeHtml(h)).join('<br>') : '&nbsp;'}</td>
                        </tr>
                    `).join('') || '<tr><td colspan="3">&nbsp;</td></tr>'}
                </tbody>
            </table>
        `;

        html += sectionTitle('Otros estudios:');
        html += `
            <table class="cv-table">
                <thead>
                    <tr>
                        <th>Nombre del curso/seminario</th>
                        <th>Institución que lo impartió</th>
                        <th>Fecha de estudios</th>
                    </tr>
                </thead>
                <tbody>
                    ${otrosEstudios.map(item => `
                        <tr>
                            <td>${isSelected('atestados', item.id, 'titulo') ? escapeHtml(item.titulo) : ''}</td>
                            <td>${isSelected('atestados', item.id, 'institucion') ? escapeHtml(item.institucion) : ''}</td>
                            <td>${isSelected('atestados', item.id, 'fecha_fin') ? escapeHtml(item.fecha_fin) : ''}</td>
                        </tr>
                    `).join('') || '<tr><td colspan="3">&nbsp;</td></tr>'}
                </tbody>
            </table>
        `;

        if (mostrarPaisesExperiencia) {
            html += sectionTitle('Países donde tiene experiencia de trabajo los últimos 10 años:');
            html += `<p>${paisesExperiencia.length ? paisesExperiencia.map(p => escapeHtml(p)).join(', ') : '&nbsp;'}</p>`;
        }

        html += sectionTitle('Historia laboral:');
        html += `
            <table class="cv-table">
                <thead>
                    <tr>
                        <th>Desde</th>
                        <th>Hasta</th>
                        <th>Empresa</th>
                        <th>Cargos desempeñados</th>
                    </tr>
                </thead>
                <tbody>
                    ${renderRows('experiencias', data.experiencias, ['desde', 'hasta', 'empresa', 'cargo']) || '<tr><td colspan="4">&nbsp;</td></tr>'}
                </tbody>
            </table>
        `;

        html += sectionTitle('Experiencia en consultorías y gestión de proyectos:');
        html += `
            <table class="cv-table">
                <thead>
                    <tr>
                        <th>Consultorías / capacitaciones</th>
                        <th>Empresa / organización</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    ${consultorias.map(item => `
                        <tr>
                            <td>${isSelected('atestados', item.id, 'titulo') ? escapeHtml(item.titulo) : ''}</td>
                            <td>${isSelected('atestados', item.id, 'institucion') ? escapeHtml(item.institucion) : ''}</td>
                            <td>${isSelected('atestados', item.id, 'fecha_fin') ? escapeHtml(item.fecha_fin) : ''}</td>
                        </tr>
                    `).join('') || '<tr><td colspan="3">&nbsp;</td></tr>'}
                </tbody>
            </table>
        `;

        html += sectionTitle('Experiencia como facilitador/a:');
        html += `
            <table class="cv-table">
                <thead>
                    <tr>
                        <th>Nombre de la capacitación</th>
                        <th>Fecha inicio</th>
                        <th>Fecha fin</th>
                        <th>Empresa a quien se impartió</th>
                    </tr>
                </thead>
                <tbody>
                    ${renderRows('capacitaciones_fepade', data.capacitaciones_fepade, ['nombre_evento', 'fecha_inicio', 'fecha_fin', 'institucion']) || '<tr><td colspan="4">&nbsp;</td></tr>'}
                </tbody>
            </table>
        `;

        html += renderContact();

        renderTemplateVisibility();

        html += `</div>`;

        return html;
    }

    function tipoFormacionIds(...keys) {
        return keys.flatMap(key => TIPO_FORMACION[key] || []).map(Number);
    }

    function selectedAtestadosByTypes(typeIds) {
        return window.cvData.atestados.filter(item =>
            typeIds.includes(Number(item.id_tipo_formacion))
            && rowIsVisible('atestados', item, [
                'tipo_formacion', 'tipo_atestado', 'nivel', 'titulo', 'institucion',
                'descripcion', 'pais', 'fecha_inicio', 'fecha_fin', 'fecha_emision',
                'fecha_vencimiento', 'horas', 'archivo_url'
            ])
        );
    }

    function selectedExperiencias() {
        return window.cvData.experiencias.filter(item =>
            rowIsVisible('experiencias', item, [
                'empresa', 'cargo', 'descripcion', 'desde', 'hasta',
                'jefe_nombre', 'jefe_email', 'jefe_telefono'
            ])
        );
    }

    function selectedAreasWithSkills() {
        return window.cvData.areas
            .filter(area => isSelected('areas', area.id, 'nombre') && area.nombre)
            .map(area => {
                const habilidades = area.habilidades
                    .filter(hab => isSelected(`habilidades_area_${area.id}`, hab.id, 'nombre') && hab.nombre)
                    .map(hab => hab.nombre);

                return { ...area, habilidades };
            });
    }

    function selectedIdiomas() {
        return window.cvData.idiomas.filter(item =>
            rowIsVisible('idiomas', item, ['idioma', 'nivel', 'certificado_url'])
        );
    }

    function emptyText() {
        return '<span class="cv-placeholder-text">No registrado</span>';
    }

    function renderMineducyt() {
        const data = window.cvData;
        const educacionFormal = selectedAtestadosByTypes(tipoFormacionIds('educacion_formal'));
        const formacionComplementaria = selectedAtestadosByTypes(tipoFormacionIds(
            'acreditacion', 'educacion_continua', 'capacitacion_recibida'
        ));
        const experiencias = selectedExperiencias();
        const areas = selectedAreasWithSkills();
        const idiomas = selectedIdiomas();
        const cargoNumero = manualValue('cargo_numero');
        const nombreConsultoria = manualValue('nombre_consultoria');
        const tareas = manualValue('tareas_asignadas');

        let html = `
            <div class="cv-institutional-document">
                <div class="cv-institutional-header">
                    <div class="cv-institutional-brand">FEPADE</div>
                    <h1>CURRÍCULUM DEL PERSONAL PROPUESTO</h1>
                    <p>Formato CV MINEDUCYT / BIRF</p>
                </div>
        `;

        html += `
            <div class="cv-institutional-process">
                <strong>Consultoría o proceso:</strong>
                ${nombreConsultoria || '<span class="cv-placeholder-text">Pendiente de completar por FEPADE</span>'}
            </div>

            <h2 class="cv-numbered-title"><span>1</span> Información general</h2>
            <table class="cv-table cv-table-clean cv-info-table">
                <tr><th>Nombre del cargo y número:</th><td>${cargoNumero || '<span class="cv-placeholder-text">Pendiente de completar por FEPADE</span>'}</td></tr>
                <tr><th>Nombre del Experto:</th><td>${personal('nombre') || '&nbsp;'}</td></tr>
                <tr><th>Fecha de nacimiento:</th><td>${personal('fecha_nacimiento') || '&nbsp;'}</td></tr>
                <tr><th>País de ciudadanía/residencia:</th><td>${personal('nacionalidad') || personal('residencia') || '&nbsp;'}</td></tr>
            </table>
        `;

        html += `<h2 class="cv-numbered-title"><span>2</span> Educación</h2>`;
        html += `
            <table class="cv-table cv-institutional-table">
                <thead><tr><th>Título obtenido</th><th>Institución</th><th>Fecha de estudios</th></tr></thead>
                <tbody>
                    ${educacionFormal.map(item => `
                        <tr>
                            <td>${isSelected('atestados', item.id, 'titulo') ? escapeHtml(item.titulo) : ''}</td>
                            <td>${isSelected('atestados', item.id, 'institucion') ? escapeHtml(item.institucion) : ''}</td>
                            <td>${isSelected('atestados', item.id, 'fecha_fin') ? escapeHtml(item.fecha_fin) : ''}</td>
                        </tr>
                    `).join('') || `<tr><td colspan="3">${emptyText()}</td></tr>`}
                </tbody>
            </table>
        `;

        html += `<h2 class="cv-numbered-title"><span>3</span> Formación complementaria</h2>`;
        html += `
            <table class="cv-table cv-institutional-table">
                <thead><tr><th>Curso, seminario o certificación</th><th>Institución</th><th>Fecha</th></tr></thead>
                <tbody>
                    ${formacionComplementaria.map(item => `
                        <tr>
                            <td>${isSelected('atestados', item.id, 'titulo') ? escapeHtml(item.titulo) : ''}</td>
                            <td>${isSelected('atestados', item.id, 'institucion') ? escapeHtml(item.institucion) : ''}</td>
                            <td>${isSelected('atestados', item.id, 'fecha_fin') ? escapeHtml(item.fecha_fin) : (isSelected('atestados', item.id, 'fecha_emision') ? escapeHtml(item.fecha_emision) : '')}</td>
                        </tr>
                    `).join('') || `<tr><td colspan="3">${emptyText()}</td></tr>`}
                </tbody>
            </table>
        `;

        html += `<h2 class="cv-numbered-title"><span>4</span> Experiencia laboral pertinente para el trabajo</h2>`;
        html += `
            <table class="cv-table cv-institutional-table cv-experience-table">
                <thead><tr><th>Período</th><th>Entidad empleadora y referencias</th><th>País</th><th>Resumen</th></tr></thead>
                <tbody>
                    ${experiencias.map(item => {
                        const referencias = [
                            isSelected('experiencias', item.id, 'jefe_nombre') && item.jefe_nombre ? `<strong>Referencia:</strong> ${escapeHtml(item.jefe_nombre)}` : '',
                            isSelected('experiencias', item.id, 'jefe_telefono') && item.jefe_telefono ? `<strong>Teléfono:</strong> ${escapeHtml(item.jefe_telefono)}` : '',
                            isSelected('experiencias', item.id, 'jefe_email') && item.jefe_email ? `<strong>Correo:</strong> ${escapeHtml(item.jefe_email)}` : ''
                        ].filter(Boolean).join('<br>');

                        return `
                            <tr>
                                <td>${isSelected('experiencias', item.id, 'desde') ? escapeHtml(item.desde) : ''}${isSelected('experiencias', item.id, 'hasta') && item.hasta ? ' – ' + escapeHtml(item.hasta) : ''}</td>
                                <td>
                                    ${isSelected('experiencias', item.id, 'empresa') && item.empresa ? `<strong>${escapeHtml(item.empresa)}</strong><br>` : ''}
                                    ${isSelected('experiencias', item.id, 'cargo') && item.cargo ? `${escapeHtml(item.cargo)}` : ''}
                                    ${referencias ? `<div class="cv-cell-reference">${referencias}</div>` : ''}
                                </td>
                                <td>${item.pais ? escapeHtml(item.pais) : emptyText()}</td>
                                <td>${isSelected('experiencias', item.id, 'descripcion') && item.descripcion ? escapeHtml(item.descripcion) : '&nbsp;'}</td>
                            </tr>
                        `;
                    }).join('') || `<tr><td colspan="4">${emptyText()}</td></tr>`}
                </tbody>
            </table>
        `;

        html += `<h2 class="cv-numbered-title"><span>5</span> Asociaciones profesionales y publicaciones</h2>`;
        html += `
            <table class="cv-table cv-institutional-table">
                <tr><th style="width:32%">Asociaciones profesionales</th><td>${emptyText()}</td></tr>
                <tr><th>Publicaciones</th><td>${emptyText()}</td></tr>
            </table>
        `;

        html += `<h2 class="cv-numbered-title"><span>6</span> Idiomas</h2>`;
        html += `
            <table class="cv-table cv-institutional-table">
                <thead><tr><th>Idioma</th><th>Nivel</th><th>Certificado</th></tr></thead>
                <tbody>
                    ${idiomas.map(item => `
                        <tr>
                            <td>${isSelected('idiomas', item.id, 'idioma') ? escapeHtml(item.idioma) : ''}</td>
                            <td>${isSelected('idiomas', item.id, 'nivel') ? escapeHtml(item.nivel) : ''}</td>
                            <td>${isSelected('idiomas', item.id, 'certificado_url') && item.certificado_url ? `<a href="${escapeHtml(item.certificado_url)}" target="_blank">Ver certificado</a>` : emptyText()}</td>
                        </tr>
                    `).join('') || `<tr><td colspan="3">${emptyText()}</td></tr>`}
                </tbody>
            </table>
        `;

        const habilidades = areas.flatMap(area => area.habilidades);
        html += `<h2 class="cv-numbered-title"><span>7</span> Idoneidad para el trabajo</h2>`;
        html += `
            <table class="cv-table cv-institutional-table cv-suitability-table">
                <tr><th>Tareas detalladas asignadas</th><td class="cv-manual-value">${tareas || '<span class="cv-placeholder-text">Pendiente de completar por FEPADE</span>'}</td></tr>
                <tr><th>Áreas de especialización relevantes</th><td>${areas.length ? areas.map(area => `<span class="cv-inline-tag">${escapeHtml(area.nombre)}</span>`).join(' ') : emptyText()}</td></tr>
                <tr><th>Habilidades técnicas relacionadas</th><td>${habilidades.length ? [...new Set(habilidades)].map(h => `<span class="cv-inline-tag cv-inline-tag-muted">${escapeHtml(h)}</span>`).join(' ') : emptyText()}</td></tr>
            </table>
        `;

        html += `<h2 class="cv-numbered-title"><span>8</span> Información de contacto</h2>`;
        html += renderContact() || `<p>${emptyText()}</p>`;

        html += `
            <h2 class="cv-numbered-title"><span>9</span> Certificación</h2>
            <p>Yo, la/el abajo firmante, certifico que este currículum describe correctamente mi persona, mis calificaciones y mi experiencia.</p>
            <div class="cv-signature-block">
                <div>Nombre del Consultor/a</div><div>Firma</div><div>Fecha</div>
            </div>
            <div class="cv-signature-block cv-signature-authority">
                <div><strong>Ana María Porras de Bardi</strong><br>Directora Ejecutiva y Representante Legal<br>FEPADE</div><div>Firma</div><div>Fecha</div>
            </div>
        `;

        html += `</div>`;
        return html;
    }

    function renderResumen() {
        const educacionFormal = selectedAtestadosByTypes(tipoFormacionIds('educacion_formal'));
        const especialidades = selectedAtestadosByTypes(tipoFormacionIds(
            'acreditacion', 'educacion_continua', 'capacitacion_recibida'
        ));
        const experiencias = selectedExperiencias();
        const areas = selectedAreasWithSkills();
        const nombreConsultoria = manualValue('nombre_consultoria');
        const cargoPropuesto = manualValue('cargo_propuesto');
        const actividades = manualValue('actividades_consultoria');

        const educationHtml = educacionFormal.length
            ? `<ul class="cv-compact-list">${educacionFormal.map(item => `<li><strong>${isSelected('atestados', item.id, 'titulo') ? escapeHtml(item.titulo) : ''}</strong>${isSelected('atestados', item.id, 'institucion') && item.institucion ? ` — ${escapeHtml(item.institucion)}` : ''}${isSelected('atestados', item.id, 'fecha_fin') && item.fecha_fin ? ` (${escapeHtml(item.fecha_fin)})` : ''}</li>`).join('')}</ul>`
            : emptyText();

        const specialtyParts = [];
        especialidades.forEach(item => {
            specialtyParts.push(`<li><strong>${isSelected('atestados', item.id, 'titulo') ? escapeHtml(item.titulo) : ''}</strong>${isSelected('atestados', item.id, 'institucion') && item.institucion ? ` — ${escapeHtml(item.institucion)}` : ''}</li>`);
        });
        areas.forEach(area => {
            specialtyParts.push(`<li><strong>${escapeHtml(area.nombre)}</strong>${area.habilidades.length ? `: ${area.habilidades.map(escapeHtml).join(', ')}` : ''}</li>`);
        });

        const jobsHtml = experiencias.length
            ? `<ul class="cv-compact-list">${experiencias.map(item => `<li>${isSelected('experiencias', item.id, 'cargo') && item.cargo ? `<strong>${escapeHtml(item.cargo)}</strong>` : ''}${isSelected('experiencias', item.id, 'empresa') && item.empresa ? ` — ${escapeHtml(item.empresa)}` : ''}${(isSelected('experiencias', item.id, 'desde') || isSelected('experiencias', item.id, 'hasta')) ? ` <span class="cv-muted">(${isSelected('experiencias', item.id, 'desde') ? escapeHtml(item.desde) : ''}${isSelected('experiencias', item.id, 'hasta') && item.hasta ? ' – ' + escapeHtml(item.hasta) : ''})</span>` : ''}</li>`).join('')}</ul>`
            : emptyText();

        let html = `
            <div class="cv-summary-document">
                <div class="cv-summary-header">
                    <div class="cv-summary-brand">FEPADE</div>
                    <h1>RESUMEN DEL CV DEL PERSONAL PROPUESTO</h1>
                    <p>Documento de síntesis para oferta o consultoría</p>
                </div>

                <div class="cv-summary-context">
                    <div><strong>Consultoría o proceso</strong><span>${nombreConsultoria || 'Pendiente de completar por FEPADE'}</span></div>
                    <div><strong>Cargo propuesto</strong><span>${cargoPropuesto || 'Pendiente de completar por FEPADE'}</span></div>
                </div>

                <table class="cv-table cv-summary-table">
                    <tr class="cv-summary-section-row"><th colspan="2">Identificación</th></tr>
                    <tr><th>Nombre del Oferente</th><td>Fundación Empresarial para el Desarrollo Educativo -FEPADE-</td></tr>
                    <tr><th>Nombre del profesional propuesto</th><td>${personal('nombre') || '&nbsp;'}</td></tr>
                    <tr><th>Nacionalidad</th><td>${personal('nacionalidad') || emptyText()}</td></tr>

                    <tr class="cv-summary-section-row"><th colspan="2">Formación y experiencia</th></tr>
                    <tr><th>Educación</th><td>${educationHtml}</td></tr>
                    <tr><th>Asociaciones profesionales a las que pertenece</th><td>${emptyText()}</td></tr>
                    <tr><th>Otras especialidades</th><td>${specialtyParts.length ? `<ul class="cv-compact-list">${specialtyParts.join('')}</ul>` : emptyText()}</td></tr>
                    <tr><th>Países donde tiene experiencia de trabajo</th><td>${emptyText()}</td></tr>
                    <tr><th>Trabajos que ha realizado</th><td>${jobsHtml}</td></tr>

                    <tr class="cv-summary-section-row"><th colspan="2">Asignación de la consultoría</th></tr>
                    <tr><th>Detalle de las actividades asignadas en esta consultoría</th><td class="cv-manual-value">${actividades || '<span class="cv-placeholder-text">Pendiente de completar por FEPADE</span>'}</td></tr>
                </table>
            </div>
        `;

        return html;
    }

    function renderProfesional() {
        const data = window.cvData;

        const iniciales = personal('nombre')
            ? personal('nombre').split(' ').filter(Boolean).slice(0, 2).map(p => p.charAt(0)).join('').toUpperCase()
            : 'CV';

        const habilidadesPreview = [];

        document.querySelectorAll('.cv-habilidad-toggle').forEach(input => {
            const areaId = input.dataset.area;
            const habId = input.dataset.item;

            if (!isSelected('areas', areaId, 'nombre')) {
                return;
            }

            if (!isSelected(`habilidades_area_${areaId}`, habId, 'nombre')) {
                return;
            }

            const label = input.closest('.form-check')?.querySelector('label');
            const nombre = label ? label.textContent.trim() : '';

            if (nombre) {
                habilidadesPreview.push(nombre);
            }
        });

        const referenciasPorTipo = {};

        data.referencias
            .filter(item => rowIsVisible('referencias', item, ['tipo', 'nombre', 'telefono', 'correo', 'empresa', 'cargo']))
            .forEach(item => {
                const tipo = item.tipo || 'Sin tipo';
                referenciasPorTipo[tipo] ??= [];
                referenciasPorTipo[tipo].push(item);
            });

        let html = `
            <div class="cv-pro-document">
                <div class="cv-pro-hero">
                    <table class="cv-pro-hero-table">
                        <tr>
                            <td class="cv-pro-hero-photo-cell">
                                <div class="cv-pro-hero-photo">
                                    ${data.personal.foto && isSelected('personal', 'personal', 'foto')
                                        ? `<img src="${escapeHtml(data.personal.foto)}" alt="Foto">`
                                        : `<div class="cv-pro-hero-photo-empty">${iniciales}</div>`
                                    }
                                </div>
                            </td>

                            <td class="cv-pro-hero-info-cell">
                                <div class="cv-pro-hero-brand">CONSULTORES FEPADE 2026</div>

                                <h1 class="cv-pro-hero-name">${personal('nombre') || 'Consultor FEPADE'}</h1>

                                <div class="cv-pro-hero-role">Consultor/a profesional FEPADE</div>

                                <div class="cv-pro-hero-meta">
                                    ${personal('nacionalidad') ? `<span><strong>Nacionalidad</strong>${personal('nacionalidad')}</span>` : ''}
                                    ${(personal('residencia_completa') || personal('residencia')) ? `<span><strong>Residencia</strong>${personal('residencia_completa') || personal('residencia')}</span>` : ''}
                                    ${personal('fecha_nacimiento') ? `<span><strong>Nacimiento</strong>${personal('fecha_nacimiento')}</span>` : ''}
                                </div>
                            </td>
                        </tr>
                    </table>

                    <div class="cv-pro-hero-footer">
                        Documento generado automáticamente por el Sistema de Gestión de Consultores FEPADE
                    </div>
                </div>
        `;

        const emailsContacto = [];
        const telefonosContacto = [];

        data.emails.forEach(item => {
            if (isSelected('emails', item.id, 'email') && item.email) {
                emailsContacto.push(escapeHtml(item.email));
            }
        });

        data.telefonos.forEach(item => {
            if (rowIsVisible('telefonos', item, ['tipo', 'extension', 'numero'])) {
                const tipo = isSelected('telefonos', item.id, 'tipo') && item.tipo ? `${escapeHtml(item.tipo)}: ` : '';
                const extension = isSelected('telefonos', item.id, 'extension') && item.extension ? `(${escapeHtml(item.extension)}) ` : '';
                const numero = isSelected('telefonos', item.id, 'numero') && item.numero ? escapeHtml(item.numero) : '';

                telefonosContacto.push(`${tipo}${extension}${numero}`);
            }
        });

        html += `<div class="cv-pro-summary-bar">`;

        if (emailsContacto.length || telefonosContacto.length) {
            html += `
                <div class="cv-pro-summary-section">
                    <div class="cv-pro-summary-title">Información de contacto</div>
                    ${emailsContacto.map(item => `<span class="cv-pro-summary-pill">Correo: ${item}</span>`).join('')}
                    ${telefonosContacto.map(item => `<span class="cv-pro-summary-pill">${item}</span>`).join('')}
                </div>
            `;
        }

        if (personal('direccion')) {
            html += `
                <div class="cv-pro-summary-section">
                    <div class="cv-pro-summary-title">Dirección de residencia</div>
                    <span class="cv-pro-summary-pill">${personal('direccion')}</span>
                </div>
            `;
        }

        const areas = data.areas.filter(area => isSelected('areas', area.id, 'nombre') && area.nombre);

        if (areas.length) {
            html += `
                <div class="cv-pro-summary-section">
                    <div class="cv-pro-summary-title">Áreas de especialización</div>
                    ${areas.map(area => `<span class="cv-pro-summary-pill-accent">${escapeHtml(area.nombre)}</span>`).join('')}
                </div>
            `;
        }

        if (habilidadesPreview.length) {
            html += `
                <div class="cv-pro-summary-section">
                    <div class="cv-pro-summary-title">Habilidades técnicas</div>

                    ${[...new Set(habilidadesPreview)].map(habilidad => `
                        <span class="cv-pro-summary-pill">
                            ${escapeHtml(habilidad)}
                        </span>
                    `).join('')}
                </div>
            `;
        }

        const idiomas = data.idiomas.filter(item => rowIsVisible('idiomas', item, ['idioma', 'nivel', 'certificado_url']));

        if (idiomas.length) {
            html += `
                <div class="cv-pro-summary-section">
                    <div class="cv-pro-summary-title">Idiomas</div>
                    ${idiomas.map(item => `
                        <span class="cv-pro-summary-pill">
                            ${isSelected('idiomas', item.id, 'idioma') && item.idioma ? escapeHtml(item.idioma) : ''}
                            ${isSelected('idiomas', item.id, 'nivel') && item.nivel ? ' — ' + escapeHtml(item.nivel) : ''}
                            ${isSelected('idiomas', item.id, 'certificado_url') && item.certificado_url ? `
                                <br>
                                <a class="cv-pro-link" href="${escapeHtml(item.certificado_url)}" target="_blank">
                                    Ver atestado
                                </a>
                            ` : ''}
                        </span>
                    `).join('')}
                </div>
            `;
        }

        const disponibilidades = data.disponibilidades.filter(item => rowIsVisible('disponibilidades', item, ['nombre']));

        if (disponibilidades.length) {
            html += `
                <div class="cv-pro-summary-section">
                    <div class="cv-pro-summary-title">Disponibilidad</div>
                    ${disponibilidades.map(item => `<span class="cv-pro-summary-pill">${escapeHtml(item.nombre)}</span>`).join('')}
                </div>
            `;
        }

        html += `</div>`;

        const experiencias = data.experiencias.filter(item => rowIsVisible('experiencias', item, ['empresa', 'cargo', 'descripcion', 'desde', 'hasta', 'jefe_nombre', 'jefe_email', 'jefe_telefono']));

        if (experiencias.length) {
            html += `
                <div class="cv-pro-section">
                    <h2 class="cv-pro-section-title">Experiencia profesional</h2>
                    ${experiencias.map(item => `
                        <div class="cv-pro-item">
                            ${(isSelected('experiencias', item.id, 'desde') || isSelected('experiencias', item.id, 'hasta')) ? `
                                <div class="cv-pro-date">
                                    ${isSelected('experiencias', item.id, 'desde') ? escapeHtml(item.desde) : ''}
                                    ${isSelected('experiencias', item.id, 'hasta') && item.hasta ? ' - ' + escapeHtml(item.hasta) : ''}
                                </div>
                            ` : ''}

                            ${isSelected('experiencias', item.id, 'cargo') ? `<div class="cv-pro-item-title">${escapeHtml(item.cargo)}</div>` : ''}
                            ${isSelected('experiencias', item.id, 'empresa') ? `<div class="cv-pro-place">${escapeHtml(item.empresa)}</div>` : ''}
                            ${isSelected('experiencias', item.id, 'descripcion') ? `<div>${escapeHtml(item.descripcion)}</div>` : ''}

                            ${(isSelected('experiencias', item.id, 'jefe_nombre') || isSelected('experiencias', item.id, 'jefe_email') || isSelected('experiencias', item.id, 'jefe_telefono')) ? `
                                <div class="cv-pro-small">
                                    ${isSelected('experiencias', item.id, 'jefe_nombre') && item.jefe_nombre ? `Referencia: ${escapeHtml(item.jefe_nombre)}` : ''}
                                    ${isSelected('experiencias', item.id, 'jefe_telefono') && item.jefe_telefono ? ` | Tel. ${escapeHtml(item.jefe_telefono)}` : ''}
                                    ${isSelected('experiencias', item.id, 'jefe_email') && item.jefe_email ? ` | ${escapeHtml(item.jefe_email)}` : ''}
                                    </div>
                                ` : ''}

                                <div class="clear"></div>
                            </div>
                        `).join('')}
                    </div>
                `;
        }

        const atestados = data.atestados.filter(item => rowIsVisible('atestados', item, ['tipo_formacion', 'tipo_atestado', 'nivel', 'titulo', 'institucion', 'descripcion', 'pais', 'fecha_inicio', 'fecha_fin', 'fecha_emision', 'fecha_vencimiento', 'horas', 'archivo_url']));

        if (atestados.length) {
            html += `
                <div class="cv-pro-section">
                    <h2 class="cv-pro-section-title">Formación, atestados y trayectoria profesional</h2>
                    ${atestados.map(item => `
                        <div class="cv-pro-item">
                            ${(isSelected('atestados', item.id, 'fecha_inicio') || isSelected('atestados', item.id, 'fecha_fin')) ? `
                                <div class="cv-pro-date">
                                    ${isSelected('atestados', item.id, 'fecha_inicio') ? escapeHtml(item.fecha_inicio) : ''}
                                    ${isSelected('atestados', item.id, 'fecha_fin') && item.fecha_fin ? ' - ' + escapeHtml(item.fecha_fin) : ''}
                                </div>
                            ` : ''}

                            ${isSelected('atestados', item.id, 'titulo') ? `<div class="cv-pro-item-title">${escapeHtml(item.titulo)}</div>` : ''}
                            ${isSelected('atestados', item.id, 'institucion') ? `<div class="cv-pro-place">${escapeHtml(item.institucion)}</div>` : ''}

                            <div class="cv-pro-tags">
                                ${isSelected('atestados', item.id, 'tipo_formacion') && item.tipo_formacion ? `<span class="cv-pro-tag">${escapeHtml(item.tipo_formacion)}</span>` : ''}
                                ${isSelected('atestados', item.id, 'tipo_atestado') && item.tipo_atestado ? `<span class="cv-pro-tag">${escapeHtml(item.tipo_atestado)}</span>` : ''}
                                ${isSelected('atestados', item.id, 'nivel') && item.nivel ? `<span class="cv-pro-tag">${escapeHtml(item.nivel)}</span>` : ''}
                                ${isSelected('atestados', item.id, 'pais') && item.pais ? `<span class="cv-pro-tag">${escapeHtml(item.pais)}</span>` : ''}
                                ${isSelected('atestados', item.id, 'horas') && item.horas ? `<span class="cv-pro-tag">${escapeHtml(item.horas)} horas</span>` : ''}
                            </div>

                            ${(isSelected('atestados', item.id, 'fecha_emision') || isSelected('atestados', item.id, 'fecha_vencimiento')) ? `
                                <div class="cv-pro-small">
                                    ${isSelected('atestados', item.id, 'fecha_emision') && item.fecha_emision ? `Emisión: ${escapeHtml(item.fecha_emision)}` : ''}
                                    ${isSelected('atestados', item.id, 'fecha_vencimiento') && item.fecha_vencimiento ? ` | Vence: ${escapeHtml(item.fecha_vencimiento)}` : ''}
                                </div>
                            ` : ''}

                            ${isSelected('atestados', item.id, 'descripcion') && item.descripcion ? `<div>${escapeHtml(item.descripcion)}</div>` : ''}

                            ${isSelected('atestados', item.id, 'archivo_url') && item.archivo_url ? `
                                <div class="cv-pro-small">
                                    <a class="cv-pro-link" href="${escapeHtml(item.archivo_url)}" target="_blank">Ver atestado</a>
                                </div>
                            ` : ''}

                            <div class="clear"></div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        const capacitaciones = data.capacitaciones_fepade.filter(item => rowIsVisible('capacitaciones_fepade', item, ['nombre_evento', 'tema', 'institucion', 'modalidad', 'fecha_inicio', 'fecha_fin', 'horas', 'fuente']));

        if (capacitaciones.length) {
            html += `
                <div class="cv-pro-section">
                    <h2 class="cv-pro-section-title">Capacitaciones FEPADE</h2>
                    ${capacitaciones.map(item => `
                        <div class="cv-pro-item">
                            ${(isSelected('capacitaciones_fepade', item.id, 'fecha_inicio') || isSelected('capacitaciones_fepade', item.id, 'fecha_fin')) ? `
                                <div class="cv-pro-date">
                                    ${isSelected('capacitaciones_fepade', item.id, 'fecha_inicio') ? escapeHtml(item.fecha_inicio) : ''}
                                    ${isSelected('capacitaciones_fepade', item.id, 'fecha_fin') && item.fecha_fin ? ' - ' + escapeHtml(item.fecha_fin) : ''}
                                </div>
                            ` : ''}

                            ${isSelected('capacitaciones_fepade', item.id, 'nombre_evento') ? `<div class="cv-pro-item-title">${escapeHtml(item.nombre_evento)}</div>` : ''}
                            ${isSelected('capacitaciones_fepade', item.id, 'institucion') ? `<div class="cv-pro-place">${escapeHtml(item.institucion)}</div>` : ''}

                            <div class="cv-pro-tags">
                                ${isSelected('capacitaciones_fepade', item.id, 'tema') && item.tema ? `<span class="cv-pro-tag">${escapeHtml(item.tema)}</span>` : ''}
                                ${isSelected('capacitaciones_fepade', item.id, 'modalidad') && item.modalidad ? `<span class="cv-pro-tag">${escapeHtml(item.modalidad)}</span>` : ''}
                                ${isSelected('capacitaciones_fepade', item.id, 'horas') && item.horas ? `<span class="cv-pro-tag">${escapeHtml(item.horas)} horas</span>` : ''}
                                ${isSelected('capacitaciones_fepade', item.id, 'fuente') && item.fuente ? `<span class="cv-pro-tag">${escapeHtml(item.fuente)}</span>` : ''}
                            </div>

                            <div class="clear"></div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        if (Object.keys(referenciasPorTipo).length) {
            html += `
                <div class="cv-pro-section">
                    <h2 class="cv-pro-section-title">Referencias</h2>

                    ${Object.entries(referenciasPorTipo).map(([tipo, items]) => `
                        <div class="cv-pro-ref-type">${escapeHtml(tipo)}</div>

                        <div class="cv-pro-grid">
                            ${items.map(item => `
                                <div class="cv-pro-ref">
                                    ${isSelected('referencias', item.id, 'nombre') ? `<strong>${escapeHtml(item.nombre)}</strong>` : ''}
                                    ${isSelected('referencias', item.id, 'cargo') && item.cargo ? `<span>${escapeHtml(item.cargo)}</span>` : ''}
                                    ${isSelected('referencias', item.id, 'empresa') && item.empresa ? `<span>${escapeHtml(item.empresa)}</span>` : ''}
                                    ${isSelected('referencias', item.id, 'telefono') && item.telefono ? `<span>${escapeHtml(item.telefono)}</span>` : ''}
                                    ${isSelected('referencias', item.id, 'correo') && item.correo ? `<span>${escapeHtml(item.correo)}</span>` : ''}
                                </div>
                            `).join('')}
                        </div>
                    `).join('')}
                </div>
            `;
        }

        html += `</div>`;

        return html;
    }

    function renderContact() {
        const emails = window.cvData.emails
            .filter(item => rowIsVisible('emails', item, ['email']))
            .map(item => escapeHtml(item.email))
            .join('<br>');

        const telefonos = window.cvData.telefonos
            .filter(item => rowIsVisible('telefonos', item, ['tipo', 'extension', 'numero']))
            .map(item => {
                const tipo = isSelected('telefonos', item.id, 'tipo') && item.tipo ? escapeHtml(item.tipo) + ': ' : '';
                const extension = isSelected('telefonos', item.id, 'extension') && item.extension ? '(' + escapeHtml(item.extension) + ') ' : '';
                const numero = isSelected('telefonos', item.id, 'numero') && item.numero ? escapeHtml(item.numero) : '';

                return `${tipo}${extension}${numero}`;
            })
            .filter(Boolean)
            .join('<br>');

        if (!emails && !telefonos) return '';

        return `
            <h2>Información de contacto:</h2>
            ${emails ? `<p><strong>Correo:</strong><br>${emails}</p>` : ''}
            ${telefonos ? `<p><strong>Teléfono:</strong><br>${telefonos}</p>` : ''}
        `;
    }

    const templateSections = {
        fepade: [
            'personal',
            'emails',
            'telefonos',
            'experiencias',
            'atestados',
            'capacitaciones_fepade',
            'areas',
            'fepade_opciones'
        ],

        mineducyt_birf: [
            'personal',
            'emails',
            'telefonos',
            'experiencias',
            'atestados',
            'areas',
            'idiomas'
        ],

        resumen_personal: [
            'personal',
            'experiencias',
            'atestados',
            'areas'
        ],

        profesional: [
            'personal',
            'emails',
            'telefonos',
            'experiencias',
            'atestados',
            'capacitaciones_fepade',
            'areas',
            'idiomas',
            'referencias',
            'disponibilidades'
        ]
    };

    const configSectionCollapseMap = {
        cvDatos: 'personal',
        cvEmails: 'emails',
        cvTelefonos: 'telefonos',
        cvExperiencia: 'experiencias',
        cvAtestados: 'atestados',
        cvFepade: 'capacitaciones_fepade',
        cvIdiomas: 'idiomas',
        cvReferencias: 'referencias',
        cvDisponibilidad: 'disponibilidades',
        cvFepadeOpciones: 'fepade_opciones',
        cvAreas: 'areas'
    };

    function initConfigSectionMetadata() {
        Object.entries(configSectionCollapseMap).forEach(([collapseId, section]) => {
            const collapse = document.getElementById(collapseId);
            const item = collapse?.closest('.accordion-item');

            if (item) {
                item.dataset.cvSection = section;
            }
        });
    }

    function renderConfigSections() {
        const allowedSections = templateSections[state.plantilla] || [];

        document.querySelectorAll('#cvAccordion .accordion-item[data-cv-section]').forEach(item => {
            const visible = allowedSections.includes(item.dataset.cvSection);
            item.hidden = !visible;

            if (!visible) {
                const collapse = item.querySelector('.accordion-collapse.show');

                if (collapse && window.bootstrap?.Collapse) {
                    bootstrap.Collapse.getOrCreateInstance(collapse, { toggle: false }).hide();
                }
            }
        });
    }

    function render() {
        state.plantilla = plantillaSelect.value;
        templateName.textContent = window.cvPlantillas[state.plantilla];
        
        renderTemplateVisibility();
        renderManualFieldsVisibility();
        renderConfigSections();

        let html = '';

        if (state.plantilla === 'mineducyt_birf') {
            html = renderMineducyt();
        } else if (state.plantilla === 'resumen_personal') {
            html = renderResumen();
        } else if (state.plantilla === 'profesional') {
            html = renderProfesional();
        } else {
            html = renderFepade();
        }

        preview.innerHTML = html;
        configInput.value = JSON.stringify(state);

    }

    function setAll(checked) {
        document.querySelectorAll('.cv-toggle').forEach(input => {
            setToggle(input, checked);
        });
    }

    function expandAll() {
        document.querySelectorAll('#cvAccordion .accordion-collapse').forEach(el => {
            bootstrap.Collapse.getOrCreateInstance(el, { toggle: false }).show();
        });
    }

    function collapseAll() {
        document.querySelectorAll('#cvAccordion .accordion-collapse').forEach(el => {
            bootstrap.Collapse.getOrCreateInstance(el, { toggle: false }).hide();
        });
    }

    initSelections();
    initConfigSectionMetadata();
    updateTrajectoryFilterUi();
    render();

    plantillaSelect.addEventListener('change', () => render());

    document.querySelectorAll('.cv-period-filter [data-years]').forEach(button => {
        button.addEventListener('click', function () {
            applyTrajectoryPeriodFilter(this.dataset.years);
        });
    });

    document.querySelectorAll('.cv-manual-input').forEach(input => {
        input.addEventListener('input', function () {
            state.campos_manual[this.dataset.manualField] = this.value;
            render();
        });
    });

    document.querySelectorAll('.cv-toggle').forEach(input => {
        input.addEventListener('change', function () {
            const section = this.dataset.section;
            const item = this.dataset.item;
            const field = this.dataset.field;

            state.selections[section] ??= {};
            state.selections[section][item] ??= {};
            state.selections[section][item][field] = this.checked;

            render();
        });
    });

    document.getElementById('btnSelectAllCv').addEventListener('click', function () {
        setAll(true);
        render();
    });

    document.getElementById('btnClearAllCv').addEventListener('click', function () {
        setAll(false);
        render();
    });

    document.getElementById('btnExpandAllCv').addEventListener('click', expandAll);
    document.getElementById('btnCollapseAllCv').addEventListener('click', collapseAll);

    document.querySelectorAll('.cv-section-select').forEach(button => {
        button.addEventListener('click', function () {
            setSection(this.dataset.section, true);
            render();
        });
    });

    document.querySelectorAll('.cv-section-clear').forEach(button => {
        button.addEventListener('click', function () {
            setSection(this.dataset.section, false);
            render();
        });
    });

    document.querySelectorAll('.cv-item-select').forEach(button => {
        button.addEventListener('click', function () {
            setItem(this.dataset.section, this.dataset.item, true);
            render();
        });
    });

    document.querySelectorAll('.cv-item-clear').forEach(button => {
        button.addEventListener('click', function () {
            setItem(this.dataset.section, this.dataset.item, false);
            render();
        });
    });

    document.querySelectorAll('.cv-area-select').forEach(button => {
        button.addEventListener('click', function () {
            setArea(this.dataset.area, true);
            render();
        });
    });

    document.querySelectorAll('.cv-area-clear').forEach(button => {
        button.addEventListener('click', function () {
            setArea(this.dataset.area, false);
            render();
        });
    });

    document.querySelectorAll('.cv-area-toggle').forEach(input => {
        input.addEventListener('change', function () {
            syncAreaRules(this.dataset.item);
            render();
        });
    });

    document.querySelectorAll('.cv-habilidad-toggle').forEach(input => {
        input.addEventListener('change', function () {

            const areaId = this.dataset.area;

            if (!isSelected('areas', areaId, 'nombre')) {
                setToggle(this, false);
            }

            render();
        });
    });


    document.getElementById('btnGenerarPdf').addEventListener('click', function () {
        configInput.value = JSON.stringify(state);
        pdfForm.submit();
    });
});
</script>

<style>

    .cv-builder {
        height: calc(100vh - 165px);
        min-height: 680px;
        overflow: hidden;
    }

    .cv-preview-panel,
    .cv-config-panel {
        min-height: 0;
        height: 100%;
    }

    .cv-preview-panel {
        display: flex;
        flex-direction: column;
    }

    .cv-preview-toolbar {
        flex: 0 0 auto;
    }

    .cv-paper-wrap {
        flex: 1 1 auto;
        min-height: 0;
        overflow: auto;
        scroll-behavior: smooth;
        overscroll-behavior: contain;
    }

    .cv-config-sticky {
        position: sticky;
        top: 82px;
        height: 100%;
        max-height: none;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        padding-right: 0;
    }

    .cv-config-toolbar {
        flex: 0 0 auto;
        max-height: 46%;
        overflow-y: auto;
        padding-right: .35rem;
        scrollbar-gutter: stable;
    }

    .cv-config-sections {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        overscroll-behavior: contain;
        scrollbar-gutter: stable;
        padding-right: .35rem;
        border-top: 1px solid var(--surface2);
        padding-top: .75rem;
    }

    .cv-config-toolbar::-webkit-scrollbar,
    .cv-config-sections::-webkit-scrollbar,
    .cv-paper-wrap::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .cv-config-toolbar::-webkit-scrollbar-thumb,
    .cv-config-sections::-webkit-scrollbar-thumb,
    .cv-paper-wrap::-webkit-scrollbar-thumb {
        background: rgba(101, 98, 100, .35);
        border-radius: 999px;
    }


    [hidden] {
        display: none !important;
    }


    .cv-config-panel {
        min-width: 0;
    }

    .cv-config-sticky {
        position: sticky;
        top: 82px;
        max-height: calc(100vh - 98px);
        overflow-y: auto;
        overscroll-behavior: contain;
        scrollbar-gutter: stable;
        padding-right: .35rem;
    }

    .cv-config-sticky::-webkit-scrollbar {
        width: 8px;
    }

    .cv-config-sticky::-webkit-scrollbar-thumb {
        background: rgba(101, 98, 100, .35);
        border-radius: 999px;
    }

    .cv-filter-card,
    .cv-template-manual-fields {
        border-left: 4px solid var(--fepade-green, #00C896);
    }

    .cv-period-filter .btn {
        white-space: nowrap;
    }

    .cv-manual-input {
        resize: vertical;
        min-height: 120px;
    }

    .cv-manual-value {
        white-space: pre-wrap;
    }

    .cv-placeholder-text {
        color: #7a7f87;
        font-style: italic;
    }
    .cv-builder {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 390px;
        gap: 1.5rem;
        align-items: start;
    }

    .cv-preview-panel {
        min-width: 0;
    }

    .cv-preview-toolbar {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
    }

    .cv-preview-toolbar span {
        display: block;
        color: var(--muted);
        font-size: .85rem;
    }

    .cv-paper-wrap {
        background: #dfe5ec;
        border-radius: 18px;
        padding: 2rem;
        overflow-x: auto;
    }

    .cv-paper {
        width: 816px;
        min-height: 1056px;
        margin: 0 auto;
        background: #ffffff;
        padding: 48px;
        box-shadow: 0 16px 42px rgba(13, 27, 42, .18);
        font-family: Arial, sans-serif;
        font-size: 13px;
        color: #111;
    }

    .cv-title {
        text-align: center;
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 24px;
    }

    .cv-paper h2 {
        font-size: 15px;
        font-weight: 700;
        margin: 20px 0 8px;
    }

    .cv-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 12px;
    }

    .cv-table th,
    .cv-table td {
        border: 1px solid #333;
        padding: 7px;
        vertical-align: top;
    }

    .cv-table th {
        background: #f1f3f5;
    }

    .cv-table-clean th,
    .cv-table-clean td {
        border: 0;
        background: transparent;
        text-align: left;
    }

    .cv-table-clean th {
        width: 220px;
    }

    .cv-config-panel {
        min-width: 0;
    }

    .cv-config-sticky {
        position: sticky;
        top: 82px;
    }

    .cv-config-item {
        border: 1px solid var(--surface2);
        background: var(--surface);
        border-radius: 14px;
        padding: .75rem;
        margin-bottom: .75rem;
    }

    .cv-check {
        margin-bottom: .35rem;
    }

    .cv-check:last-child {
        margin-bottom: 0;
    }

    @media (max-width: 1199px) {
        .cv-builder {
            grid-template-columns: 1fr;
        }

        .cv-config-sticky {
            position: static;
        }
    }

    .cv-pro {
        display: grid;
        grid-template-columns: 250px 1fr;
        min-height: 950px;
    }

    .cv-pro-sidebar {
        background: #0D1B2A;
        color: #ffffff;
        padding: 28px 22px;
    }

    .cv-pro-sidebar h3 {
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: .06em;
        border-bottom: 1px solid rgba(255,255,255,.25);
        padding-bottom: 7px;
        margin: 22px 0 10px;
    }

    .cv-pro-sidebar ul {
        padding-left: 18px;
        margin: 0;
    }

    .cv-pro-sidebar li {
        margin-bottom: 6px;
    }

    .cv-pro-photo {
        width: 145px;
        height: 145px;
        border-radius: 50%;
        overflow: hidden;
        border: 5px solid #ffffff;
        margin: 0 auto 22px;
        background: #eef2f8;
    }

    .cv-pro-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cv-pro-photo-empty {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0D1B2A;
        font-weight: 800;
        font-size: 32px;
    }

    .cv-pro-main {
        padding: 34px;
    }

    .cv-pro-header {
        border-bottom: 3px solid #0D1B2A;
        padding-bottom: 18px;
        margin-bottom: 22px;
    }

    .cv-pro-header h1 {
        margin: 0;
        color: #0D1B2A;
        font-size: 30px;
        font-weight: 800;
        letter-spacing: .02em;
    }

    .cv-pro-header p {
        margin: 5px 0 10px;
        color: #0065cc;
        font-weight: 700;
        text-transform: uppercase;
    }

    .cv-pro-header span {
        display: inline-block;
        margin-right: 12px;
        color: #555;
    }

    .cv-pro-main section {
        margin-bottom: 24px;
    }

    .cv-pro-main h2 {
        color: #0D1B2A;
        border-bottom: 1px solid #cbd5e1;
        padding-bottom: 6px;
        margin-bottom: 12px;
        font-size: 16px;
        text-transform: uppercase;
    }

    .cv-pro-item {
        margin-bottom: 15px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e5e7eb;
    }

    .cv-pro-item-head {
        display: flex;
        justify-content: space-between;
        gap: 14px;
    }

    .cv-pro-item-head strong {
        color: #0D1B2A;
        font-size: 14px;
    }

    .cv-pro-item-head span {
        color: #555;
        white-space: nowrap;
    }

    .cv-pro-place {
        color: #0065cc;
        font-weight: 700;
        margin: 3px 0;
    }

    .cv-pro-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .cv-pro-ref {
        border: 1px solid #d8dee4;
        border-radius: 10px;
        padding: 10px;
    }

    .cv-pro-ref strong,
    .cv-pro-ref span {
        display: block;
    }

    .cv-paper .cv-pro-document {
        width: 100%;
        font-family: Arial, sans-serif;
        font-size: 11px;
        color: #0D1B2A;
        line-height: 1.38;
    }

    .cv-paper .cv-pro-hero {
        background: #0D1B2A;
        margin: -48px -48px 18px -48px;
        padding: 30px 48px 18px;
        border-bottom: 6px solid #00C896;
        color: #FFFFFF;
    }

    .cv-paper .cv-pro-hero-table {
        width: 100%;
        border-collapse: collapse;
    }

    .cv-paper .cv-pro-hero-photo-cell {
        width: 132px;
        vertical-align: middle;
        padding-right: 24px;
    }

    .cv-paper .cv-pro-hero-info-cell {
        vertical-align: middle;
    }

    .cv-paper .cv-pro-hero-photo {
        width: 108px;
        height: 108px;
        border-radius: 54px;
        overflow: hidden;
        background: #FFFFFF;
        border: 4px solid #FFFFFF;
        text-align: center;
    }

    .cv-paper .cv-pro-hero-photo img {
        width: 108px;
        height: 108px;
        object-fit: cover;
    }

    .cv-paper .cv-pro-hero-photo-empty {
        height: 108px;
        line-height: 108px;
        color: #0D1B2A;
        font-size: 30px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .cv-paper .cv-pro-hero-brand {
        color: #00C896;
        font-size: 8.5px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .14em;
        margin-bottom: 7px;
    }

    .cv-paper .cv-pro-hero-name {
        margin: 0;
        color: #FFFFFF;
        font-size: 27px;
        line-height: 1.05;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .035em;
    }

    .cv-paper .cv-pro-hero-role {
        display: inline-block;
        margin-top: 8px;
        margin-bottom: 12px;
        padding: 3px 10px;
        border: 1px solid #00C896;
        border-radius: 12px;
        color: #FFFFFF;
        font-size: 9.5px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .cv-paper .cv-pro-hero-meta span {
        display: inline-block;
        margin: 0 18px 6px 0;
        color: #EEF2F8;
        font-size: 9px;
    }

    .cv-paper .cv-pro-hero-meta strong {
        display: block;
        color: #00C896;
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: .07em;
        margin-bottom: 2px;
    }

    .cv-paper .cv-pro-hero-footer {
        margin-top: 16px;
        padding-top: 8px;
        border-top: 1px solid rgba(255,255,255,.18);
        color: #EEF2F8;
        font-size: 8.5px;
        text-align: right;
    }

    .cv-paper .cv-pro-summary-section {
        margin-bottom: 10px;
        padding: 10px 12px;
        background: #F7F9FC;
        border: 1px solid rgba(13, 27, 42, 0.10);
        border-left: 4px solid #00C896;
    }

    .cv-paper .cv-pro-summary-title {
        font-size: 9.5px;
        font-weight: bold;
        text-transform: uppercase;
        color: #0D1B2A;
        margin-bottom: 7px;
        letter-spacing: .07em;
    }

    .cv-paper .cv-pro-summary-pill,
    .cv-paper .cv-pro-summary-pill-accent {
        display: inline-block;
        border-radius: 12px;
        padding: 3px 9px;
        margin: 0 5px 5px 0;
        font-size: 9px;
    }

    .cv-paper .cv-pro-summary-pill {
        background: #FFFFFF;
        border: 1px solid rgba(13, 27, 42, 0.10);
        color: #0D1B2A;
    }

    .cv-paper .cv-pro-summary-pill-accent {
        background: #EEF2F8;
        border: 1px solid rgba(13, 27, 42, 0.10);
        color: #0099FF;
        font-weight: bold;
    }

    .cv-paper .cv-pro-section {
        margin-top: 18px;
    }

    .cv-paper .cv-pro-section-title {
        margin: 0 0 12px;
        padding: 7px 10px;
        background: #162032;
        color: #FFFFFF;
        font-size: 11.5px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .055em;
        border-left: 5px solid #00C896;
    }

    .cv-paper .cv-pro-item {
        position: relative;
        margin-bottom: 11px;
        padding: 10px 12px;
        background: #FFFFFF;
        border: 1px solid rgba(13, 27, 42, 0.10);
        border-left: 4px solid #00C896;
    }

    .cv-paper .cv-pro-item-title {
        font-size: 11.5px;
        font-weight: bold;
        color: #0D1B2A;
        margin-right: 120px;
    }

    .cv-paper .cv-pro-place {
        color: #0099FF;
        font-weight: bold;
        margin: 3px 0;
    }

    .cv-paper .cv-pro-date {
        float: right;
        max-width: 120px;
        text-align: center;
        font-size: 9px;
        font-weight: bold;
        color: #0D1B2A;
        background: #EEF2F8;
        border: 1px solid rgba(13, 27, 42, 0.10);
        border-radius: 10px;
        padding: 2px 8px;
    }

    .cv-paper .cv-pro-small {
        margin-top: 5px;
        color: #6B7A90;
        font-size: 9px;
    }

    .cv-paper .cv-pro-tags {
        margin-top: 6px;
    }

    .cv-paper .cv-pro-tag {
        display: inline-block;
        margin: 2px 3px 2px 0;
        padding: 2px 8px;
        border-radius: 9px;
        background: #F7F9FC;
        border: 1px solid rgba(13, 27, 42, 0.10);
        color: #0D1B2A;
        font-size: 8.7px;
        font-weight: bold;
    }

    .cv-paper .cv-pro-link {
        color: #0099FF;
        text-decoration: none;
        font-weight: bold;
    }

    .cv-paper .cv-pro-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    .cv-paper .cv-pro-ref {
        background: #F7F9FC;
        border: 1px solid rgba(13, 27, 42, 0.10);
        border-top: 3px solid #162032;
        padding: 9px 10px;
    }

    .cv-paper .cv-pro-ref strong,
    .cv-paper .cv-pro-ref span {
        display: block;
    }

    .cv-paper .cv-pro-ref strong {
        color: #0D1B2A;
        font-size: 10.5px;
    }

    .cv-paper .cv-pro-ref span {
        color: #6B7A90;
        font-size: 9px;
    }

    .cv-paper .cv-pro-ref-type {
        display: inline-block;
        margin: 8px 0 6px;
        padding: 2px 9px;
        border-radius: 9px;
        background: #0D1B2A;
        color: #FFFFFF;
        font-size: 8.7px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .cv-section-actions {
        display: flex;
        gap: .5rem;
        margin-bottom: .9rem;
    }

    .cv-config-item-header {
        display: flex;
        justify-content: space-between;
        gap: .75rem;
        align-items: start;
        margin-bottom: .65rem;
    }

    .cv-config-item-actions {
        display: flex;
        gap: .35rem;
        flex-shrink: 0;
    }

    .cv-config-fields {
        display: grid;
        gap: .25rem;
    }

    .cv-config-group-title {
        margin: 1rem 0 .5rem;
        padding: .35rem .55rem;
        border-radius: .65rem;
        background: var(--surface2);
        color: var(--text);
        font-weight: 700;
        font-size: .82rem;
        text-transform: uppercase;
    }

    .cv-config-subtitle {
        margin: .75rem 0 .35rem;
        color: var(--muted);
        font-weight: 700;
        font-size: .8rem;
    }

    .cv-paper .cv-fepade-document {
        width: 100%;
        font-family: Arial, sans-serif;
        font-size: 11px;
        color: #0D1B2A;
        line-height: 1.38;
    }

    .cv-paper .cv-fepade-hero {
        background: #0D1B2A;
        margin: -48px -48px 20px -48px;
        padding: 28px 48px 22px;
        border-bottom: 6px solid #00C896;
        color: #FFFFFF;
    }

    .cv-paper .cv-fepade-brand {
        color: #00C896;
        font-size: 8.5px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .14em;
        margin-bottom: 8px;
    }

    .cv-paper .cv-fepade-hero h1 {
        margin: 0;
        color: #FFFFFF;
        font-size: 28px;
        line-height: 1.05;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .035em;
    }

    .cv-paper .cv-fepade-hero p {
        display: inline-block;
        margin: 10px 0 0;
        padding: 3px 10px;
        border: 1px solid #00C896;
        border-radius: 12px;
        color: #FFFFFF;
        font-size: 9.5px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .cv-paper .cv-fepade-document h2 {
        margin: 18px 0 12px;
        padding: 7px 10px;
        background: #162032;
        color: #FFFFFF;
        font-size: 11.5px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: .055em;
        border-left: 5px solid #00C896;
    }

    .cv-paper .cv-fepade-document .cv-table {
        border-collapse: collapse;
        margin-bottom: 14px;
    }

    .cv-paper .cv-fepade-document .cv-table th {
        background: #EEF2F8;
        color: #0D1B2A;
        font-weight: bold;
    }

    .cv-paper .cv-fepade-document .cv-table td,
    .cv-paper .cv-fepade-document .cv-table th {
        border: 1px solid rgba(13, 27, 42, 0.22);
        padding: 7px;
        vertical-align: top;
    }

    .cv-paper .cv-fepade-document .cv-table-clean {
        background: #F7F9FC;
        border-left: 4px solid #00C896;
        margin-bottom: 18px;
    }

    .cv-paper .cv-fepade-document .cv-table-clean th,
    .cv-paper .cv-fepade-document .cv-table-clean td {
        border: 0;
        background: transparent;
    }

    .cv-paper .cv-fepade-document .cv-table-clean th {
        color: #0D1B2A;
        font-weight: bold;
        width: 220px;
    }

    @media (max-width: 1199.98px) {
        .cv-config-sticky {
            position: static;
            max-height: none;
            overflow: visible;
            padding-right: 0;
        }
    }



    @media (max-width: 1199.98px) {
        .cv-builder {
            height: auto;
            min-height: 0;
            overflow: visible;
        }

        .cv-preview-panel,
        .cv-config-panel {
            height: auto;
        }

        .cv-paper-wrap {
            max-height: none;
            overflow-x: auto;
            overflow-y: visible;
        }

        .cv-config-sticky {
            position: static;
            height: auto;
            display: block;
            overflow: visible;
        }

        .cv-config-toolbar,
        .cv-config-sections {
            max-height: none;
            overflow: visible;
            padding-right: 0;
        }
    }


    @media (min-width: 1200px) {
        .cv-builder {
            height: calc(100vh - 165px);
            min-height: 680px;
            overflow: hidden;
            align-items: stretch;
        }

        .cv-preview-panel,
        .cv-config-panel {
            height: 100%;
            min-height: 0;
        }

        .cv-preview-panel {
            display: flex;
            flex-direction: column;
        }

        .cv-paper-wrap {
            flex: 1 1 auto;
            min-height: 0;
            overflow: auto;
        }

        .cv-config-sticky {
            position: sticky;
            top: 82px;
            height: 100%;
            max-height: none;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding-right: 0;
        }

        .cv-config-toolbar {
            flex: 0 0 auto;
            max-height: 46%;
            overflow-y: auto;
        }

        .cv-config-sections {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
        }
    }
    .cv-institutional-document,
    .cv-summary-document {
        color: #0D1B2A;
        font-family: Arial, sans-serif;
    }

    .cv-institutional-header,
    .cv-summary-header {
        margin: -48px -48px 20px;
        padding: 24px 48px 18px;
        background: #0D1B2A;
        border-bottom: 6px solid #00C896;
        color: #fff;
    }

    .cv-institutional-brand,
    .cv-summary-brand {
        color: #00C896;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .18em;
    }

    .cv-institutional-header h1,
    .cv-summary-header h1 {
        margin: 6px 0 4px;
        color: #fff;
        font-size: 21px;
    }

    .cv-institutional-header p,
    .cv-summary-header p {
        margin: 0;
        color: #EEF2F8;
        font-size: 10px;
    }

    .cv-institutional-process {
        padding: 10px 12px;
        margin-bottom: 16px;
        background: #F7F9FC;
        border-left: 4px solid #00C896;
    }

    .cv-numbered-title {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 7px 10px;
        margin: 17px 0 9px !important;
        background: #162032;
        border-left: 5px solid #00C896;
        color: #fff !important;
        font-size: 12px !important;
    }

    .cv-numbered-title span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #00C896;
        color: #fff;
    }

    .cv-institutional-table thead th,
    .cv-summary-section-row th {
        background: #162032;
        border-left: 5px solid #00C896;
        color: #fff;
    }

    .cv-institutional-table tbody tr:nth-child(even) td {
        background: #F7F9FC;
    }

    .cv-cell-reference {
        margin-top: 6px;
        padding-top: 5px;
        border-top: 1px solid rgba(13, 27, 42, .16);
        color: #6B7A90;
        font-size: 10px;
    }

    .cv-inline-tag {
        display: inline-block;
        padding: 3px 7px;
        margin: 2px 3px 2px 0;
        border-radius: 10px;
        background: #EEF2F8;
        color: #0D1B2A;
        font-size: 9px;
        font-weight: 700;
    }

    .cv-inline-tag-muted {
        background: #F7F9FC;
        color: #0D1B2A;
    }

    .cv-signature-block {
        display: grid;
        grid-template-columns: 1.8fr 1fr 1fr;
        gap: 14px;
        margin-top: 34px;
    }

    .cv-signature-block > div {
        padding-top: 7px;
        border-top: 1px solid #0D1B2A;
        text-align: center;
        font-size: 9px;
    }

    .cv-signature-authority {
        margin-top: 42px;
    }

    .cv-summary-context {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 14px;
    }

    .cv-summary-context > div {
        padding: 10px;
        background: #F7F9FC;
        border-top: 3px solid #00C896;
    }

    .cv-summary-context strong,
    .cv-summary-context span {
        display: block;
    }

    .cv-summary-context strong {
        margin-bottom: 4px;
        color: #0D1B2A;
        font-size: 9px;
        text-transform: uppercase;
    }

    .cv-summary-table th:first-child:not([colspan]) {
        width: 34%;
        background: #EEF2F8;
        color: #0D1B2A;
        text-align: left;
    }

    .cv-summary-section-row th {
        padding: 8px 10px;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .cv-compact-list {
        margin: 0;
        padding-left: 18px;
    }

    .cv-compact-list li {
        margin-bottom: 5px;
    }

    .cv-muted {
        color: #6B7A90;
        font-size: 10px;
    }

</style>

@endsection