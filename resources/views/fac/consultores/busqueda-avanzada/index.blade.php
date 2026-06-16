@extends('layouts.app')

@section('title', 'Búsqueda avanzada | Facilitadores FEPADE')
@section('page-title', 'Búsqueda avanzada')
@section('page-subtitle', 'Filtra consultores por perfil profesional, experiencia, ubicación, formación, idiomas y disponibilidad.')


@push('styles')
<style>
    #resultadosConsultores {
        position: relative;
        transition: opacity .2s ease;
    }

    #resultadosConsultores.busqueda-resultados-loading {
        opacity: .55;
        pointer-events: none;
    }
</style>
@endpush

@section('content')

<x-ui.page-header
    title="Búsqueda avanzada"
    subtitle="Filtra consultores por perfil profesional, experiencia, ubicación, formación, idiomas y disponibilidad."
/>

<form method="GET" action="{{ route('fac.consultores.busqueda-avanzada') }}" id="formBusquedaAvanzada" data-url-base="{{ route('fac.consultores.busqueda-avanzada') }}">
    <div class="fepade-card busqueda-toolbar mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-lg-8">
                <label class="form-label">Búsqueda general</label>
                <div class="input-group">
                    <input
                        type="text"
                        class="form-control"
                        name="q"
                        placeholder="Nombre, apellido, documento, NIT, NRC o correo..."
                        value="{{ $filtros['q'] ?? '' }}"
                    >
                    <button class="btn btn-fepade" type="submit">
                        <i class="fas fa-search me-1"></i> Buscar
                    </button>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('fac.consultores.busqueda-avanzada') }}" class="btn btn-outline-secondary w-100 w-lg-auto">
                    <i class="fas fa-eraser me-1"></i> Limpiar filtros
                </a>
            </div>
        </div>
    </div>

    <div class="busqueda-layout">
        <aside class="busqueda-filtros fepade-card">
            <div class="busqueda-filtros-header">
                <div>
                    <h5>Filtros avanzados</h5>
                    <p>Combina filtros para encontrar perfiles específicos.</p>
                </div>
            </div>

            <div class="accordion accordion-flush" id="accordionFiltros">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#filtroFechas">
                            Fechas
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

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filtroPerfil">
                            Información general
                        </button>
                    </h2>
                    <div id="filtroPerfil" class="accordion-collapse collapse" data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label">Sexo</label>
                                <select name="sexo" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($sexos as $sexo)
                                        <option value="{{ $sexo->id_sexo }}" @selected(($filtros['sexo'] ?? null) == $sexo->id_sexo)>{{ $sexo->nombre }}</option>
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

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filtroUbicacion">
                            Ubicación y disponibilidad
                        </button>
                    </h2>
                    <div id="filtroUbicacion" class="accordion-collapse collapse" data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label">País</label>
                                <select id="pais" name="pais" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($paises as $pais)
                                        <option value="{{ $pais->id_pais }}" @selected(($filtros['pais'] ?? null) == $pais->id_pais)>{{ $pais->nombre_pais }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Departamento</label>
                                <select id="departamento" name="departamento" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($departamentos as $departamento)
                                        <option value="{{ $departamento->id_departamento }}" @selected(($filtros['departamento'] ?? null) == $departamento->id_departamento)>{{ $departamento->nombre_departamento }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Municipio</label>
                                <select id="municipio_mh" name="municipio_mh" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($municipiosMh as $municipio)
                                        <option value="{{ $municipio->id_municipio_mh }}" @selected(($filtros['municipio_mh'] ?? null) == $municipio->id_municipio_mh)>{{ $municipio->municipio_mh_nombre }}</option>
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
                                        <option value="{{ $tipo->id_tipo_disponibilidad }}" @selected(($filtros['disponibilidad'] ?? null) == $tipo->id_tipo_disponibilidad)>{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filtroHabilidades">
                            Habilidades y áreas
                        </button>
                    </h2>
                    <div id="filtroHabilidades" class="accordion-collapse collapse" data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">
                            @include('fac.consultores.busqueda-avanzada.partials._checkbox_habilidades', [
                                'titulo' => 'Áreas de especialización',
                                'nombre' => 'area_especializacion',
                                'items' => $areaEspecializacion,
                            ])

                            @include('fac.consultores.busqueda-avanzada.partials._checkbox_habilidades', [
                                'titulo' => 'Habilidades técnicas',
                                'nombre' => 'habilidades_tecnicas',
                                'items' => $habilidadesTecnicas,
                            ])

                            @include('fac.consultores.busqueda-avanzada.partials._checkbox_habilidades', [
                                'titulo' => 'Habilidades blandas',
                                'nombre' => 'habilidades_blandas',
                                'items' => $habilidadesBlandas,
                            ])
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filtroEducacion">
                            Educación y atestados
                        </button>
                    </h2>
                    <div id="filtroEducacion" class="accordion-collapse collapse" data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label">Tipo de formación</label>
                                <select name="tipo_formacion" class="form-select">
                                    <option value="">Todas</option>
                                    @foreach($tiposFormacion as $tipo)
                                        <option value="{{ $tipo->id_tipo_formacion }}" @selected(($filtros['tipo_formacion'] ?? null) == $tipo->id_tipo_formacion)>{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nivel académico</label>
                                <select name="nivel_academico" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($nivelesAcademicos as $nivel)
                                        <option value="{{ $nivel->id_nivel_academico }}" @selected(($filtros['nivel_academico'] ?? null) == $nivel->id_nivel_academico)>{{ $nivel->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Tipo de atestado</label>
                                <select name="tipo_atestado" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($tiposAtestado as $atestado)
                                        <option value="{{ $atestado->id_tipo_atestado }}" @selected(($filtros['tipo_atestado'] ?? null) == $atestado->id_tipo_atestado)>{{ $atestado->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filtroIdiomas">
                            Idiomas
                        </button>
                    </h2>
                    <div id="filtroIdiomas" class="accordion-collapse collapse" data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">
                            <div class="mb-3">
                                <label class="form-label">Idioma</label>
                                <select name="idioma" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($idiomas as $idioma)
                                        <option value="{{ $idioma->id_idioma }}" @selected(($filtros['idioma'] ?? null) == $idioma->id_idioma)>{{ $idioma->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Nivel mínimo</label>
                                <select name="nivel_idioma" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($nivelesIdioma as $nivel)
                                        <option value="{{ $nivel->id_idioma_nivel }}" @selected(($filtros['nivel_idioma'] ?? null) == $nivel->id_idioma_nivel)>{{ $nivel->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filtroExperiencia">
                            Experiencia profesional
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
                <a href="{{ route('fac.consultores.busqueda-avanzada') }}" class="btn btn-outline-secondary">
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

    let filtroTimer = null;
    let controller = null;
    let cargandoUbicacion = false;

    const rutas = {
        busqueda: @json(route('fac.consultores.busqueda-avanzada')),
        departamentos: @json(route('fac.ajax.departamentos')),
        municipios: @json(route('fac.ajax.municipios')),
        distritos: @json(route('fac.ajax.distritos')),
        ubicacion: @json(route('fac.ajax.ubicacion.distrito')),
    };

    function resetSelect(select, label = 'Todos') {
        if (!select) return;
        select.innerHTML = `<option value="">${label}</option>`;
    }

    function appendOption(select, value, text, data = {}) {
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

    pais?.addEventListener('change', function () {
        resetSelect(departamento);
        resetSelect(municipioMh);
        resetSelect(distrito);

        if (!this.value) {
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
        buscar(null, 0);
    });

    form?.querySelectorAll('input, select').forEach(element => {
        if (['pais', 'departamento', 'municipio_mh', 'distrito'].includes(element.id)) return;

        const eventName = element.type === 'text' || element.type === 'number' || element.type === 'date' ? 'input' : 'change';

        element.addEventListener(eventName, function () {
            if (!cargandoUbicacion) buscar();
        });
    });

    resultados?.addEventListener('click', function (event) {
        const link = event.target.closest('.pagination a');
        if (!link) return;

        event.preventDefault();
        buscar(link.href, 0);
    });
});
</script>
@endpush
