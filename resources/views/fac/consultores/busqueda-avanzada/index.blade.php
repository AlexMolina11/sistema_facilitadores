@extends('layouts.app')

@section('title', 'Búsqueda avanzada | Facilitadores FEPADE')
@section('page-title', 'Búsqueda avanzada')
@section('page-subtitle', 'Filtra consultores por perfil profesional, experiencia, ubicación, formación, idiomas y disponibilidad.')

@section('content')

<x-ui.page-header
    title="Búsqueda avanzada"
    subtitle="Filtra consultores por perfil profesional, experiencia, ubicación, formación, idiomas y disponibilidad."
/>

<form method="GET" action="{{ route('fac.consultores.busqueda-avanzada') }}" id="formBusquedaAvanzada">
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
                        value="{{ request('q') }}"
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
                                    <option value="actualizacion" @selected(request('fecha_tipo', 'actualizacion') === 'actualizacion')>Última modificación</option>
                                    <option value="creacion" @selected(request('fecha_tipo') === 'creacion')>Fecha de creación</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Filtro rápido</label>
                                <select name="fecha_filtro" class="form-select" id="fechaFiltro">
                                    <option value="">Todos</option>
                                    <option value="hoy" @selected(request('fecha_filtro') === 'hoy')>Hoy</option>
                                    <option value="7_dias" @selected(request('fecha_filtro') === '7_dias')>Últimos 7 días</option>
                                    <option value="30_dias" @selected(request('fecha_filtro') === '30_dias')>Últimos 30 días</option>
                                    <option value="este_mes" @selected(request('fecha_filtro') === 'este_mes')>Este mes</option>
                                    <option value="mes_pasado" @selected(request('fecha_filtro') === 'mes_pasado')>Mes pasado</option>
                                    <option value="personalizado" @selected(request('fecha_filtro') === 'personalizado')>Rango personalizado</option>
                                </select>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">Desde</label>
                                    <input type="date" class="form-control" name="fecha_desde" value="{{ request('fecha_desde') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Hasta</label>
                                    <input type="date" class="form-control" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
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
                                        <option value="{{ $sexo->id_sexo }}" @selected(request('sexo') == $sexo->id_sexo)>{{ $sexo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">Edad mínima</label>
                                    <input type="number" min="18" max="100" class="form-control" name="edad_min" value="{{ request('edad_min') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Edad máxima</label>
                                    <input type="number" min="18" max="100" class="form-control" name="edad_max" value="{{ request('edad_max') }}">
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
                                        <option value="{{ $pais->id_pais }}" @selected(request('pais') == $pais->id_pais)>{{ $pais->nombre_pais }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Departamento</label>
                                <select id="departamento" name="departamento" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($departamentos as $departamento)
                                        <option value="{{ $departamento->id_departamento }}" @selected(request('departamento') == $departamento->id_departamento)>{{ $departamento->nombre_departamento }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Municipio</label>
                                <select id="municipio_mh" name="municipio_mh" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($municipiosMh as $municipio)
                                        <option value="{{ $municipio->id_municipio_mh }}" @selected(request('municipio_mh') == $municipio->id_municipio_mh)>{{ $municipio->municipio_mh_nombre }}</option>
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
                                            @selected(request('distrito') == $distrito->id_municipio)
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
                                        <option value="{{ $tipo->id_tipo_disponibilidad }}" @selected(request('disponibilidad') == $tipo->id_tipo_disponibilidad)>{{ $tipo->nombre }}</option>
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
                                        <option value="{{ $tipo->id_tipo_formacion }}" @selected(request('tipo_formacion') == $tipo->id_tipo_formacion)>{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nivel académico</label>
                                <select name="nivel_academico" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($nivelesAcademicos as $nivel)
                                        <option value="{{ $nivel->id_nivel_academico }}" @selected(request('nivel_academico') == $nivel->id_nivel_academico)>{{ $nivel->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Tipo de atestado</label>
                                <select name="tipo_atestado" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($tiposAtestado as $atestado)
                                        <option value="{{ $atestado->id_tipo_atestado }}" @selected(request('tipo_atestado') == $atestado->id_tipo_atestado)>{{ $atestado->nombre }}</option>
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
                                        <option value="{{ $idioma->id_idioma }}" @selected(request('idioma') == $idioma->id_idioma)>{{ $idioma->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Nivel mínimo</label>
                                <select name="nivel_idioma" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($nivelesIdioma as $nivel)
                                        <option value="{{ $nivel->id_idioma_nivel }}" @selected(request('nivel_idioma') == $nivel->id_idioma_nivel)>{{ $nivel->nombre }}</option>
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
                                <input type="text" class="form-control" name="cargo" value="{{ request('cargo') }}" placeholder="Ej. Facilitador, consultor, gerente...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Empresa</label>
                                <input type="text" class="form-control" name="empresa" value="{{ request('empresa') }}" placeholder="Nombre de empresa o institución">
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Años mínimos de experiencia</label>
                                <input type="number" min="0" max="60" class="form-control" name="anios_experiencia" value="{{ request('anios_experiencia') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button class="btn btn-fepade" type="submit">
                    <i class="fas fa-filter me-1"></i> Aplicar filtros
                </button>
                <a href="{{ route('fac.consultores.busqueda-avanzada') }}" class="btn btn-outline-secondary">
                    Limpiar
                </a>
            </div>
        </aside>

        <section class="busqueda-resultados">
            <div class="busqueda-resultados-header fepade-card mb-3">
                <div>
                    <h5>Consultores encontrados</h5>
                    <p>{{ $totalConsultores }} resultado{{ $totalConsultores === 1 ? '' : 's' }} según los filtros aplicados.</p>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                @forelse($consultores as $consultor)
                    @php
                        $nombreCompleto = $consultor->nombre_completo ?: trim(($consultor->nombres ?? '') . ' ' . ($consultor->apellidos ?? ''));
                        $iniciales = strtoupper(mb_substr($consultor->nombres ?? 'C', 0, 1) . mb_substr($consultor->apellidos ?? 'F', 0, 1));
                        $telefonoPrincipal = optional($consultor->telefonos->first())->numero_telefono;
                        $emailPrincipal = optional($consultor->emails->firstWhere('principal', true))->email ?? optional($consultor->emails->first())->email;
                    @endphp

                    <div class="col">
                        <article class="busqueda-consultor-card">
                            <div class="busqueda-consultor-cover"></div>

                            <div class="busqueda-avatar-wrap">
                                @if($consultor->ruta_foto)
                                    <img
                                        class="busqueda-avatar-img"
                                        src="{{ \Illuminate\Support\Facades\Storage::url($consultor->ruta_foto) }}"
                                        alt="Foto de {{ $nombreCompleto }}"
                                    >
                                @else
                                    <span class="busqueda-avatar-initials">{{ $iniciales }}</span>
                                @endif
                            </div>

                            <div class="text-center mt-2">
                                <h5>{{ $nombreCompleto }}</h5>
                                <span class="badge badge-success-soft">Consultor FEPADE</span>
                            </div>

                            <div class="busqueda-consultor-meta">
                                <div>
                                    <i class="fas fa-id-card"></i>
                                    <span>{{ $consultor->numero_identificacion ?: 'Documento no registrado' }}</span>
                                </div>
                                <div>
                                    <i class="fas fa-phone"></i>
                                    <span>{{ $telefonoPrincipal ?: 'Teléfono no registrado' }}</span>
                                </div>
                                <div>
                                    <i class="fas fa-envelope"></i>
                                    <span>{{ $emailPrincipal ?: 'Correo no registrado' }}</span>
                                </div>
                            </div>

                            <div class="d-grid mt-auto">
                                <a href="{{ route('fac.consultores.show', $consultor) }}" class="btn btn-primary">
                                    Ver perfil
                                </a>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="fepade-card text-center py-5">
                            <h5 class="mb-2">No se encontraron consultores</h5>
                            <p class="text-muted mb-3">Prueba reduciendo la cantidad de filtros o limpia la búsqueda.</p>
                            <a href="{{ route('fac.consultores.busqueda-avanzada') }}" class="btn btn-outline-secondary">Limpiar filtros</a>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($consultores->hasPages())
                <div class="mt-4 fepade-pagination">
                    {{ $consultores->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </section>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pais = document.getElementById('pais');
    const departamento = document.getElementById('departamento');
    const municipioMh = document.getElementById('municipio_mh');
    const distrito = document.getElementById('distrito');

    const rutas = {
        departamentos: @json(route('fac.ajax.departamentos')),
        municipios: @json(route('fac.ajax.municipios')),
        distritos: @json(route('fac.ajax.distritos')),
        ubicacion: @json(route('fac.ajax.ubicacion.distrito')),
    };

    function resetSelect(select, label = 'Todos') {
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

    pais?.addEventListener('change', function () {
        resetSelect(departamento);
        resetSelect(municipioMh);
        resetSelect(distrito);

        if (!this.value) return;

        fetch(`${rutas.departamentos}?id_pais=${this.value}`)
            .then(response => response.json())
            .then(data => data.forEach(item => appendOption(departamento, item.id_departamento, item.nombre_departamento)));
    });

    departamento?.addEventListener('change', function () {
        resetSelect(municipioMh);
        resetSelect(distrito);

        if (!this.value) return;

        fetch(`${rutas.municipios}?id_departamento=${this.value}`)
            .then(response => response.json())
            .then(data => data.forEach(item => appendOption(municipioMh, item.id_municipio_mh, item.municipio_mh_nombre)));
    });

    municipioMh?.addEventListener('change', function () {
        resetSelect(distrito);

        if (!this.value) return;

        fetch(`${rutas.distritos}?id_municipio_mh=${this.value}`)
            .then(response => response.json())
            .then(data => data.forEach(item => appendOption(distrito, item.id_municipio, item.nombre_distrito, {
                pais: item.id_pais,
                departamento: item.id_departamento,
                municipioMh: item.id_municipio_mh,
            })));
    });

    distrito?.addEventListener('change', function () {
        if (!this.value) return;

        const option = distrito.options[distrito.selectedIndex];

        if (option.dataset.pais) {
            pais.value = option.dataset.pais;
            departamento.value = option.dataset.departamento;
            municipioMh.value = option.dataset.municipioMh;
            return;
        }

        fetch(`${rutas.ubicacion}?id_municipio=${this.value}`)
            .then(response => response.json())
            .then(data => {
                pais.value = data.pais;
                departamento.value = data.departamento;
                municipioMh.value = data.municipio_mh;
            });
    });
});
</script>
@endpush