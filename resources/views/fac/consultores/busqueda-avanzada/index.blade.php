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


    .busqueda-resultados-header {
        border-bottom: 1px solid rgba(0, 0, 0, .06);
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }

    .consultor-result-card {
        border: 1px solid rgba(56, 85, 6, .12);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 10px 24px rgba(0, 0, 0, .04);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .consultor-result-card:hover {
        transform: translateY(-2px);
        border-color: rgba(119, 145, 35, .35);
        box-shadow: 0 16px 30px rgba(0, 0, 0, .08);
    }

    .consultor-avatar {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        object-fit: cover;
        background: linear-gradient(135deg, #385506, #779123);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.05rem;
        flex: 0 0 auto;
    }

    .consultor-result-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .45rem .9rem;
        color: #656264;
        font-size: .875rem;
    }

    .consultor-result-section-title {
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #656264;
        font-weight: 700;
        margin-bottom: .35rem;
    }

    .consultor-badge {
        background: rgba(160, 197, 37, .12);
        border: 1px solid rgba(119, 145, 35, .18);
        color: #385506;
        border-radius: 999px;
        padding: .32rem .65rem;
        font-size: .78rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: .3rem;
    }

    .consultor-badge-muted {
        background: #f8f9fa;
        border-color: rgba(0,0,0,.08);
        color: #656264;
    }
</style>
@endpush

@section('content')

<x-ui.page-header
    title="Búsqueda avanzada"
    subtitle="Filtra consultores por perfil profesional, experiencia, ubicación, formación, idiomas y disponibilidad."
/>

<form method="GET" action="{{ route('fac.busqueda.index') }}" id="formBusquedaAvanzada" data-url-base="{{ route('fac.busqueda.index') }}">
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
                <a href="{{ route('fac.busqueda.index') }}" class="btn btn-outline-secondary w-100 w-lg-auto">
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

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filtroEducacion">
                            Educación y atestados
                        </button>
                    </h2>
            <div id="filtroEducacion" class="accordion-collapse collapse" data-bs-parent="#accordionFiltros">
                        <div class="accordion-body">

  {{-- NIVELES ACADÉMICOS --}}
  <div class="mb-3">

      <label class="form-label fw-bold">
          Niveles Académicos

          <span class="badge bg-primary">
              {{ count($nivelesAcademicos ?? []) }}
          </span>
      </label>

      <div
          class="border rounded p-2"
          style="max-height:250px; overflow-y:auto;"
      >

          @foreach($nivelesAcademicos ?? [] as $nivel)

              <div class="form-check">

                  <input
                      class="form-check-input"
                      type="checkbox"
                      name="nivel_academico[]"
                      value="{{ $nivel->id_nivel_academico }}"
                      id="nivel{{ $nivel->id_nivel_academico }}"
                      {{ in_array(
                          $nivel->id_nivel_academico,
                          request()->get('nivel_academico', [])
                      ) ? 'checked' : '' }}
                  >

                  <label
                      class="form-check-label"
                      for="nivel{{ $nivel->id_nivel_academico }}"
                  >
                      {{ $nivel->nombre }}
                  </label>

              </div>

          @endforeach

      </div>

  </div>

  {{-- TIPOS DE ATESTADO --}}
  <div class="mb-3">

      <label class="form-label fw-bold">
          Tipos de Atestados

          <span class="badge bg-primary">
           {{ $tiposAtestado->count() }}
          </span>
      </label>

      <div
          class="border rounded p-2"
          style="max-height:250px; overflow-y:auto;"
      >

          @foreach($tiposAtestado ?? [] as $atestado)

              <div class="form-check">

                  <input
                      class="form-check-input"
                      type="checkbox"
                      name="tipo_atestado[]"
                      value="{{ $atestado->id_tipo_atestado }}"
                      id="atestado{{ $atestado->id_tipo_atestado }}"
                      {{ in_array(
                          $atestado->id_tipo_atestado,
                          request()->get('tipo_atestado', [])
                      ) ? 'checked' : '' }}
                  >

                  <label
                      class="form-check-label"
                      for="atestado{{ $atestado->id_tipo_atestado }}"
                  >
                      {{ $atestado->nombre }}
                  </label>

              </div>

          @endforeach

      </div>
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

    <label class="form-label fw-bold">

        Idiomas

        <span class="badge bg-primary">
            {{ count($idiomas ?? []) }}
        </span>

    </label>

    <div
        class="border rounded p-2"
        style="max-height:350px; overflow-y:auto;"
    >

        @foreach($idiomas ?? [] as $idioma)

            <div class="mb-2">

                <div class="form-check">

                    <input
                        type="checkbox"
                        class="form-check-input idioma-check"
                        id="idioma{{ $idioma->id_idioma }}"
                        data-target="nivel-container-{{ $idioma->id_idioma }}"
                        {{ isset(request('idiomas')[$idioma->id_idioma]) ? 'checked' : '' }}
                    >

                    <label
                        class="form-check-label"
                        for="idioma{{ $idioma->id_idioma }}"
                    >
                        {{ $idioma->nombre }}
                    </label>

                </div>

                <div
                    id="nivel-container-{{ $idioma->id_idioma }}"
                    class="ms-4 mt-2"
                    style="
                        display:
                        {{ isset(request('idiomas')[$idioma->id_idioma]) ? 'block' : 'none' }};
                    "
                >

                    <label class="form-label small">
                        Nivel
                    </label>

                    <select
                        class="form-select form-select-sm"
                        name="idiomas[{{ $idioma->id_idioma }}]"
                    >

                        <option value="">
                            Seleccione nivel
                        </option>

                        @foreach($nivelesIdioma as $nivel)

                            <option
                                value="{{ $nivel->id_idioma_nivel }}"
                                {{
                                    (request('idiomas')[$idioma->id_idioma] ?? null)
                                    == $nivel->id_idioma_nivel
                                    ? 'selected'
                                    : ''
                                }}
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.idioma-checkbox').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const container = document.getElementById('nivel-container-' + this.value);
            if (!container) return;

            if (this.checked) {
                container.classList.remove('d-none');
            } else {
                container.classList.add('d-none');
                const select = container.querySelector('select');
                if (select) select.value = '';
            }
        });
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
        });
    });
});
</script>
@endpush
