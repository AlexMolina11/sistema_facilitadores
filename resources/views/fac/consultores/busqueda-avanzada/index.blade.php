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

                            <div class="mb-3">

    <label class="form-label fw-bold">
        Área de Especialización

        <span class="badge bg-primary">
    {{ count($areaEspecializacion ?? []) }}

</span>
    </label>

    <div
        class="border rounded p-2"
        style="max-height:250px; overflow-y:auto;"
    >

        @foreach($areaEspecializacion ?? [] as $area)

            <div class="form-check">

                <input
                    class="form-check-input"
                    type="checkbox"
                    name="area_especializacion[]"
                    value="{{ $area->id_habilidad }}"
                    id="area{{ $area->id_habilidad }}"

                    {{ in_array(
                        $area->id_habilidad,
                        request()->get('area_especializacion', [])
                    ) ? 'checked' : '' }}
                >

                <label
                    class="form-check-label"
                    for="area{{ $area->id_habilidad }}"
                >
                    {{ $area->nombre }}
                </label>

            </div>

        @endforeach

    </div>

</div>

                                <div class="mb-3">

    <label class="form-label fw-bold">
        Habilidades Blandas

<span class="badge bg-primary">
    {{ count($habilidadesBlandas ?? []) }}
</span>

    </label>

    <div
        class="border rounded p-2"
        style="max-height:250px; overflow-y:auto;"
    >

        @foreach($habilidadesBlandas ?? [] as $habilidad)

            <div class="form-check">

                <input
                    class="form-check-input"
                    type="checkbox"
                    name="habilidades_blandas[]"
                    value="{{ $habilidad->id_habilidad }}"
                    id="blanda{{ $habilidad->id_habilidad }}"

                    {{ in_array(
                        $habilidad->id_habilidad,
                        request()->get('habilidades_blandas', [])
                    ) ? 'checked' : '' }}
                >

                <label
                    class="form-check-label"
                    for="blanda{{ $habilidad->id_habilidad }}"
                >
                    {{ $habilidad->nombre }}
                </label>

            </div>

        @endforeach

    </div>

</div>

                               <div class="mb-3">

    <label class="form-label fw-bold">
        Habilidades Técnicas

<span class="badge bg-primary">
    {{ count($habilidadesTecnicas ?? []) }}
</span>

    </label>

    <div
        class="border rounded p-2"
        style="max-height:250px; overflow-y:auto;"
    >

        @foreach($habilidadesTecnicas ?? [] as $habilidad)

            <div class="form-check">

                <input
                    class="form-check-input"
                    type="checkbox"
                    name="habilidades_tecnicas[]"
                    value="{{ $habilidad->id_habilidad }}"
                    id="tecnica{{ $habilidad->id_habilidad }}"

                    {{ in_array(
                        $habilidad->id_habilidad,
                        request()->get('habilidades_tecnicas', [])
                    ) ? 'checked' : '' }}
                >

                <label
                    class="form-check-label"
                    for="tecnica{{ $habilidad->id_habilidad }}"
                >
                    {{ $habilidad->nombre }}
                </label>

            </div>

        @endforeach

    </div>

</div>

                            

                        </div>

                    </div>

                    {{-- EDUCACION --}}
                    <div class="accordion-item">

                        <h2 class="accordion-header">

                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#educacion">
                                Educación
                            </button>

                        </h2>

                        <div id="educacion" class="accordion-collapse collapse" data-bs-parent="#accordionFiltros">

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

                                    <select name="sexo" class="form-select">   
                                        
                                        <option value="">
                                            Todos
                                        </option>

                                        @foreach($sexos ?? [] as $sexo)

                                            <option value="{{ $sexo->id_sexo }}" {{ request('sexo') == $sexo->id_sexo ? 'selected' : '' }} >
                                                {{ $sexo->nombre }}
                                            </option>

                                        @endforeach

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

                        <div id="ubicacion" class="accordion-collapse collapse" data-bs-parent="#accordionFiltros">

                            <div class="accordion-body">

                                <div class="mb-3">

                                    <label class="form-label">
                                        País
                                    </label>

                                    <select id="pais" name="pais" class="form-select">
                        
                                        <option value="">
                                            Todos
                                        </option>

                                        @foreach($paises ?? [] as $pais)

                                            <option value="{{ $pais->id_pais }}" {{ request('pais') == $pais->id_pais ? 'selected' : '' }} >
                                                {{ $pais->nombre_pais }}
                                            </option>

                                        @endforeach

                                    </select>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">
                                        Departamento
                                    </label>

                                    <select id="departamento"   name="departamento"    class="form-select">

                                        <option value="">
                                            Todos
                                        </option>

                                        @foreach($departamentos ?? [] as $departamento)

                                            <option
                                                value="{{ $departamento->id_departamento }}"
                                                {{ request('departamento') == $departamento->id_departamento ? 'selected' : '' }}
                                            >
                                                {{ $departamento->nombre_departamento }}
                                            </option>

                                        @endforeach

                                    </select>

                                </div>
<div class="mb-3">

    <label class="form-label">
        Municipio
    </label>

    <select id="municipio"    name="municipio"    class="form-select">

        <option value="">
            Todos
        </option>

        @foreach($municipiosMh ?? [] as $municipio)

            <option
                value="{{ $municipio->id_municipio_mh }}"
                {{ request('municipio') == $municipio->id_municipio_mh ? 'selected' : '' }}
            >
                {{ $municipio->municipio_mh_nombre }}
            </option>

        @endforeach

    </select>

</div>

                                <div class="mb-3">

    <label class="form-label">
        Distrito
    </label>

    <select id="distrito"    name="distrito"    class="form-select">

        <option value="">
            Todos
        </option>

        @foreach($distritos ?? [] as $distrito)

<option
    value="{{ $distrito->id_municipio }}"
    data-pais="{{ $distrito->id_pais }}"
    data-departamento="{{ $distrito->id_departamento }}"
    data-municipio="{{ $distrito->id_municipio_mh }}"
>
    {{ $distrito->nombre_distrito }}
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

                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#idiomas"
                            >
                                Idiomas
                            </button>

                        </h2>

                        <div class="accordion-item">



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

                <select
                    name="idioma"
                    class="form-select"
                >

                    <option value="">
                        Todos
                    </option>

                    @foreach($idiomas ?? [] as $idioma)

                        <option
                            value="{{ $idioma->id_idioma }}"
                            {{ request('idioma') == $idioma->id_idioma ? 'selected' : '' }}
                        >
                            {{ $idioma->nombre }}
                        </option>

                    @endforeach

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Nivel
                </label>

                <select
                    name="nivel_idioma"
                    class="form-select"
                >

                    <option value="">
                        Todos
                    </option>

                    @foreach($nivelesIdioma ?? [] as $nivel)

                        <option
                            value="{{ $nivel->id_idioma_nivel }}"
                            {{ request('nivel_idioma') == $nivel->id_idioma_nivel ? 'selected' : '' }}
                        >
                            {{ $nivel->nombre }}
                        </option>

                    @endforeach

                </select>

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

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const distrito = document.getElementById('distrito');

    if (!distrito) {
        return;
    }

    distrito.addEventListener('change', function () {

        const opcion =
            distrito.options[distrito.selectedIndex];

        const pais =
            opcion.dataset.pais;

        const departamento =
            opcion.dataset.departamento;

        const municipio =
            opcion.dataset.municipio;

        document.getElementById('pais').value =
            pais;

        document.getElementById('departamento').value =
            departamento;

        document.getElementById('municipio').value =
            municipio;

    });

});

</script>

@endpush

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const pais = document.getElementById('pais');
    const departamento = document.getElementById('departamento');
    const municipio = document.getElementById('municipio');
    const distrito = document.getElementById('distrito');

    pais.addEventListener('change', function () {

        fetch('/ajax/departamentos-por-pais?id_pais=' + this.value)
        .then(response => response.json())
        .then(data => {

            departamento.innerHTML =
                '<option value="">Todos</option>';

            municipio.innerHTML =
                '<option value="">Todos</option>';

            distrito.innerHTML =
                '<option value="">Todos</option>';

            data.forEach(item => {

                departamento.innerHTML +=
                    `<option value="${item.id_departamento}">
                        ${item.nombre_departamento}
                    </option>`;

            });

        });

    });

    departamento.addEventListener('change', function () {

        fetch('/ajax/municipios-por-departamento?id_departamento=' + this.value)
        .then(response => response.json())
        .then(data => {

            municipio.innerHTML =
                '<option value="">Todos</option>';

            distrito.innerHTML =
                '<option value="">Todos</option>';

            data.forEach(item => {

                municipio.innerHTML +=
                    `<option value="${item.id_municipio_mh}">
                        ${item.municipio_mh_nombre}
                    </option>`;

            });

        });

    });

    municipio.addEventListener('change', function () {

        fetch('/ajax/distritos-por-municipio?id_municipio_mh=' + this.value)
        .then(response => response.json())
        .then(data => {

            distrito.innerHTML =
                '<option value="">Todos</option>';

            data.forEach(item => {

                distrito.innerHTML +=
                    `<option value="${item.id_municipio}">
                        ${item.nombre_distrito}
                    </option>`;

            });

        });

    });

    distrito.addEventListener('change', function () {

    if (!this.value) return;

    fetch(
        '/ajax/ubicacion-por-distrito?id_municipio=' + this.value
    )
    .then(response => response.json())
    .then(data => {

        pais.value = data.pais;

        departamento.value = data.departamento;

        municipio.value = data.municipio;

    });

    });
});

</script>

@endpush

@endsection