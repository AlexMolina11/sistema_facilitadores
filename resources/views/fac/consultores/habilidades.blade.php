@extends('layouts.app')

@section('title', 'Áreas y habilidades | Facilitadores FEPADE')
@section('page-title', 'Áreas de especialización y habilidades técnicas')
@section('page-subtitle', 'Clasifica tus evidencias de formación y experiencia.')

@section('content')

{{-- ============================================================
     WIZARD / PROGRESO GENERAL DEL PERFIL
============================================================ --}}
@include('fac.consultores.partials._wizard', [
    'step' => 5,
    'consultor' => $consultor,
])


{{-- ============================================================
     PROGRESO DE CLASIFICACIÓN
============================================================ --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">

        <div class="row align-items-center g-4">

            <div class="col-lg-8">

                <div class="d-flex align-items-start gap-3">

                    <div
                        class="d-flex align-items-center justify-content-center rounded-circle bg-light flex-shrink-0"
                        style="width: 52px; height: 52px;"
                    >
                        <i class="fa-solid fa-list-check fs-4 text-primary"></i>
                    </div>

                    <div class="flex-grow-1">

                        <h5 class="mb-1">
                            Progreso de clasificación
                        </h5>

                        @if ($progresoClasificacion['total'] > 0)

                            <p class="text-muted mb-3">
                                Has clasificado
                                <strong>
                                    {{ $progresoClasificacion['clasificadas'] }}
                                    de
                                    {{ $progresoClasificacion['total'] }}
                                </strong>
                                evidencias disponibles.
                            </p>

                            <div
                                class="progress"
                                role="progressbar"
                                aria-valuenow="{{ $progresoClasificacion['porcentaje'] }}"
                                aria-valuemin="0"
                                aria-valuemax="100"
                                style="height: 10px;"
                            >
                                <div
                                    class="progress-bar"
                                    style="width: {{ $progresoClasificacion['porcentaje'] }}%"
                                ></div>
                            </div>

                            <div class="small text-muted mt-2">
                                {{ $progresoClasificacion['porcentaje'] }}% completado
                            </div>

                        @else

                            <p class="text-muted mb-0">
                                Aún no tienes atestados ni capacitaciones FEPADE disponibles para clasificar.
                            </p>

                        @endif

                    </div>
                </div>

            </div>


            @if ($progresoClasificacion['total'] > 0)

                <div class="col-lg-4">

                    <div class="row g-2 text-center">

                        <div class="col-4">
                            <div class="border rounded-3 p-2 h-100">
                                <div class="fw-bold fs-5">
                                    {{ $progresoClasificacion['total'] }}
                                </div>
                                <div class="small text-muted">
                                    Total
                                </div>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="border rounded-3 p-2 h-100">
                                <div class="fw-bold fs-5 text-success">
                                    {{ $progresoClasificacion['clasificadas'] }}
                                </div>
                                <div class="small text-muted">
                                    Clasificadas
                                </div>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="border rounded-3 p-2 h-100">
                                <div class="fw-bold fs-5 text-warning">
                                    {{ $progresoClasificacion['pendientes'] }}
                                </div>
                                <div class="small text-muted">
                                    Pendientes
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            @endif

        </div>

    </div>
</div>


{{-- ============================================================
     ATESTADOS
============================================================ --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom p-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

            <div>
                <h5 class="mb-1">
                    <i class="fa-solid fa-file-lines me-2 text-primary"></i>
                    Atestados
                </h5>

                <div class="text-muted small">
                    Documentos de formación registrados en tu perfil.
                </div>
            </div>

            <span class="badge text-bg-light border px-3 py-2">
                {{ $progresoClasificacion['atestados']['clasificados'] }}
                de
                {{ $progresoClasificacion['atestados']['total'] }}
                clasificados
            </span>

        </div>

    </div>


    <div class="card-body p-4">

        @forelse ($atestadosEstado as $atestado)

            @php
                $clasificaciones = $atestado->clasificaciones ?? collect();
                $estaClasificado = (bool) $atestado->esta_clasificado;
            @endphp

            <div
                class="border rounded-3 p-3 mb-3 evidencia-card {{ $estaClasificado ? 'evidencia-clasificada' : 'evidencia-pendiente' }}"
            >

                <div class="row g-3 align-items-start">

                    <div class="col-lg">

                        <div class="d-flex gap-3">

                            <div class="evidencia-icono flex-shrink-0">
                                <i class="fa-solid fa-file-circle-check"></i>
                            </div>

                            <div class="flex-grow-1">

                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">

                                    <h6 class="mb-0">
                                        {{ $atestado->titulo ?: 'Atestado sin título' }}
                                    </h6>

                                    @if ($estaClasificado)
                                        <span class="badge text-bg-success">
                                            <i class="fa-solid fa-check me-1"></i>
                                            Clasificado
                                        </span>
                                    @else
                                        <span class="badge text-bg-warning">
                                            <i class="fa-solid fa-clock me-1"></i>
                                            Pendiente
                                        </span>
                                    @endif

                                </div>


                                @if ($atestado->institucion)
                                    <div class="text-muted small mb-1">
                                        <i class="fa-solid fa-building-columns me-1"></i>
                                        {{ $atestado->institucion }}
                                    </div>
                                @endif


                                <div class="d-flex flex-wrap gap-3 text-muted small">

                                    @if ($atestado->tipoFormacion?->nombre)
                                        <span>
                                            <i class="fa-solid fa-graduation-cap me-1"></i>
                                            {{ $atestado->tipoFormacion->nombre }}
                                        </span>
                                    @endif

                                    @if ($atestado->tipoAtestado?->nombre)
                                        <span>
                                            <i class="fa-solid fa-certificate me-1"></i>
                                            {{ $atestado->tipoAtestado->nombre }}
                                        </span>
                                    @endif

                                    @if ($atestado->fecha_inicio || $atestado->fecha_fin)
                                        <span>
                                            <i class="fa-regular fa-calendar me-1"></i>

                                            @if ($atestado->fecha_inicio)
                                                {{ $atestado->fecha_inicio->format('d/m/Y') }}
                                            @endif

                                            @if ($atestado->fecha_inicio && $atestado->fecha_fin)
                                                –
                                            @endif

                                            @if ($atestado->fecha_fin)
                                                {{ $atestado->fecha_fin->format('d/m/Y') }}
                                            @endif
                                        </span>
                                    @endif

                                    @if ($atestado->horas)
                                        <span>
                                            <i class="fa-regular fa-clock me-1"></i>
                                            {{ $atestado->horas }} h
                                        </span>
                                    @endif

                                </div>


                                @if ($estaClasificado)

                                    <div class="mt-3">

                                        @foreach ($clasificaciones as $clasificacion)

                                            <div class="clasificacion-resumen">

                                                @php
                                                    $habilidadesClasificacion = $clasificacion->habilidades
                                                        ->filter(fn ($item) => $item->habilidadTecnica);

                                                    $habilidades = $habilidadesClasificacion
                                                        ->pluck('habilidadTecnica.nombre');

                                                    $idsHabilidades = $habilidadesClasificacion
                                                        ->pluck('id_habilidad_tecnica')
                                                        ->values();
                                                @endphp

                                                <div class="d-flex justify-content-between align-items-start gap-3">

                                                    <div class="flex-grow-1">

                                                        <div class="fw-semibold">
                                                            <i class="fa-solid fa-layer-group me-1 text-primary"></i>
                                                            {{ $clasificacion->areaEspecializacion?->nombre ?? 'Área no disponible' }}
                                                        </div>

                                                        @if ($habilidades->isNotEmpty())

                                                            <div class="mt-2 d-flex flex-wrap gap-1">

                                                                @foreach ($habilidades as $habilidad)
                                                                    <span class="badge rounded-pill text-bg-light border fw-normal">
                                                                        {{ $habilidad }}
                                                                    </span>
                                                                @endforeach

                                                            </div>

                                                        @endif

                                                    </div>

                                                    <div class="d-flex gap-1 flex-shrink-0">

                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-primary btn-editar-clasificacion"
                                                            title="Editar clasificación"
                                                            data-clasificacion-id="{{ $clasificacion->id_consultor_area }}"
                                                            data-tipo="atestado"
                                                            data-id="{{ $atestado->id_atestado }}"
                                                            data-titulo="{{ $atestado->titulo ?: 'Atestado sin título' }}"
                                                            data-subtitulo="{{ $atestado->institucion ?? '' }}"
                                                            data-area="{{ $clasificacion->id_area_especializacion }}"
                                                            data-habilidades='@json($idsHabilidades)'
                                                        >
                                                            <i class="fa-solid fa-pen"></i>
                                                            <span class="d-none d-xl-inline ms-1">Editar</span>
                                                        </button>

                                                        <form
                                                            method="POST"
                                                            action="{{ route('fac.consultores.habilidades.destroy', [
                                                                $consultor,
                                                                $clasificacion
                                                            ]) }}"
                                                            class="d-inline"
                                                            onsubmit="return confirm('¿Estás seguro de eliminar esta clasificación? Esta acción quitará el área y las habilidades asociadas a esta evidencia.');"
                                                        >
                                                            @csrf
                                                            @method('DELETE')

                                                            <button
                                                                type="submit"
                                                                class="btn btn-sm btn-outline-danger"
                                                                title="Eliminar clasificación"
                                                            >
                                                                <i class="fa-solid fa-trash"></i>
                                                                <span class="d-none d-xl-inline ms-1">Eliminar</span>
                                                            </button>
                                                        </form>

                                                    </div>

                                                </div>

                                            </div>

                                        @endforeach

                                    </div>

                                @else

                                    <div class="small text-warning-emphasis mt-3">
                                        <i class="fa-solid fa-circle-info me-1"></i>
                                        Esta evidencia todavía no tiene un área de especialización ni habilidades técnicas asociadas.
                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>


                    <div class="col-lg-auto">

                        <button
                            type="button"
                            class="btn {{ $estaClasificado ? 'btn-outline-dark' : 'btn-dark' }} btn-clasificar-evidencia"
                            data-tipo="atestado"
                            data-id="{{ $atestado->id_atestado }}"
                            data-titulo="{{ $atestado->titulo ?: 'Atestado sin título' }}"
                            data-subtitulo="{{ $atestado->institucion ?? '' }}"
                        >
                            <i class="fa-solid {{ $estaClasificado ? 'fa-plus' : 'fa-tags' }} me-1"></i>

                            {{ $estaClasificado
                                ? 'Agregar otra clasificación'
                                : 'Clasificar evidencia'
                            }}
                        </button>

                    </div>

                </div>

            </div>

        @empty

            <div class="text-center py-4 text-muted">

                <i class="fa-regular fa-folder-open fs-2 mb-2"></i>

                <div class="fw-semibold">
                    No hay atestados disponibles.
                </div>

                <div class="small">
                    Los atestados que registres aparecerán aquí para que puedas clasificarlos.
                </div>

            </div>

        @endforelse

    </div>
</div>


{{-- ============================================================
     CAPACITACIONES FEPADE
============================================================ --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom p-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

            <div>
                <h5 class="mb-1">
                    <i class="fa-solid fa-chalkboard-user me-2 text-primary"></i>
                    Capacitaciones FEPADE
                </h5>

                <div class="text-muted small">
                    Capacitaciones registradas por FEPADE en las que has participado como facilitador.
                </div>
            </div>

            <span class="badge text-bg-light border px-3 py-2">
                {{ $progresoClasificacion['capacitaciones']['clasificadas'] }}
                de
                {{ $progresoClasificacion['capacitaciones']['total'] }}
                clasificadas
            </span>

        </div>

    </div>


    <div class="card-body p-4">

        @forelse ($capacitacionesEstado as $capacitacion)

            @php
                $clasificaciones = $capacitacion->clasificaciones ?? collect();
                $estaClasificado = (bool) $capacitacion->esta_clasificado;
            @endphp

            <div
                class="border rounded-3 p-3 mb-3 evidencia-card {{ $estaClasificado ? 'evidencia-clasificada' : 'evidencia-pendiente' }}"
            >

                <div class="row g-3 align-items-start">

                    <div class="col-lg">

                        <div class="d-flex gap-3">

                            <div class="evidencia-icono flex-shrink-0">
                                <i class="fa-solid fa-chalkboard-user"></i>
                            </div>

                            <div class="flex-grow-1">

                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">

                                    <h6 class="mb-0">
                                        {{ $capacitacion->curso_nombre ?: 'Capacitación FEPADE' }}
                                    </h6>

                                    @if ($estaClasificado)
                                        <span class="badge text-bg-success">
                                            <i class="fa-solid fa-check me-1"></i>
                                            Clasificada
                                        </span>
                                    @else
                                        <span class="badge text-bg-warning">
                                            <i class="fa-solid fa-clock me-1"></i>
                                            Pendiente
                                        </span>
                                    @endif

                                </div>


                                <div class="d-flex flex-wrap gap-3 text-muted small">

                                    @if ($capacitacion->codigo_evento)
                                        <span>
                                            <i class="fa-solid fa-hashtag me-1"></i>
                                            {{ $capacitacion->codigo_evento }}
                                        </span>
                                    @endif

                                    @if ($capacitacion->cliente)
                                        <span>
                                            <i class="fa-solid fa-building me-1"></i>
                                            {{ $capacitacion->cliente }}
                                        </span>
                                    @endif

                                    @if ($capacitacion->modalidad)
                                        <span>
                                            <i class="fa-solid fa-location-dot me-1"></i>
                                            {{ $capacitacion->modalidad }}
                                        </span>
                                    @endif

                                    @if ($capacitacion->fecha_inicio || $capacitacion->fecha_fin)
                                        <span>
                                            <i class="fa-regular fa-calendar me-1"></i>

                                            @if ($capacitacion->fecha_inicio)
                                                {{ $capacitacion->fecha_inicio->format('d/m/Y') }}
                                            @endif

                                            @if ($capacitacion->fecha_inicio && $capacitacion->fecha_fin)
                                                –
                                            @endif

                                            @if ($capacitacion->fecha_fin)
                                                {{ $capacitacion->fecha_fin->format('d/m/Y') }}
                                            @endif
                                        </span>
                                    @endif

                                    @if ($capacitacion->no_horas_real)
                                        <span>
                                            <i class="fa-regular fa-clock me-1"></i>
                                            {{ $capacitacion->no_horas_real }} h
                                        </span>
                                    @endif

                                </div>


                                @if ($estaClasificado)

                                    <div class="mt-3">

                                        @foreach ($clasificaciones as $clasificacion)

                                            <div class="clasificacion-resumen">

                                                @php
                                                    $habilidadesClasificacion = $clasificacion->habilidades
                                                        ->filter(fn ($item) => $item->habilidadTecnica);

                                                    $habilidades = $habilidadesClasificacion
                                                        ->pluck('habilidadTecnica.nombre');

                                                    $idsHabilidades = $habilidadesClasificacion
                                                        ->pluck('id_habilidad_tecnica')
                                                        ->values();
                                                @endphp

                                                <div class="d-flex justify-content-between align-items-start gap-3">

                                                    <div class="flex-grow-1">

                                                        <div class="fw-semibold">
                                                            <i class="fa-solid fa-layer-group me-1 text-primary"></i>
                                                            {{ $clasificacion->areaEspecializacion?->nombre ?? 'Área no disponible' }}
                                                        </div>

                                                        @if ($habilidades->isNotEmpty())

                                                            <div class="mt-2 d-flex flex-wrap gap-1">

                                                                @foreach ($habilidades as $habilidad)
                                                                    <span class="badge rounded-pill text-bg-light border fw-normal">
                                                                        {{ $habilidad }}
                                                                    </span>
                                                                @endforeach

                                                            </div>

                                                        @endif

                                                    </div>

                                                    <div class="d-flex gap-1 flex-shrink-0">

                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-primary btn-editar-clasificacion"
                                                            title="Editar clasificación"
                                                            data-clasificacion-id="{{ $clasificacion->id_consultor_area }}"
                                                            data-tipo="capacitacion"
                                                            data-id="{{ $capacitacion->id_capacitacion_fepade }}"
                                                            data-titulo="{{ $capacitacion->curso_nombre ?: 'Capacitación FEPADE' }}"
                                                            data-subtitulo="{{ $capacitacion->cliente ?? '' }}"
                                                            data-area="{{ $clasificacion->id_area_especializacion }}"
                                                            data-habilidades='@json($idsHabilidades)'
                                                        >
                                                            <i class="fa-solid fa-pen"></i>
                                                            <span class="d-none d-xl-inline ms-1">Editar</span>
                                                        </button>

                                                        <form
                                                            method="POST"
                                                            action="{{ route('fac.consultores.habilidades.destroy', [
                                                                $consultor,
                                                                $clasificacion
                                                            ]) }}"
                                                            class="d-inline"
                                                            onsubmit="return confirm('¿Estás seguro de eliminar esta clasificación? Esta acción quitará el área y las habilidades asociadas a esta evidencia.');"
                                                        >
                                                            @csrf
                                                            @method('DELETE')

                                                            <button
                                                                type="submit"
                                                                class="btn btn-sm btn-outline-danger"
                                                                title="Eliminar clasificación"
                                                            >
                                                                <i class="fa-solid fa-trash"></i>
                                                                <span class="d-none d-xl-inline ms-1">Eliminar</span>
                                                            </button>
                                                        </form>

                                                    </div>

                                                </div>

                                            </div>

                                        @endforeach

                                    </div>

                                @else

                                    <div class="small text-warning-emphasis mt-3">
                                        <i class="fa-solid fa-circle-info me-1"></i>
                                        Esta capacitación todavía no tiene un área de especialización ni habilidades técnicas asociadas.
                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>


                    <div class="col-lg-auto">

                        <button
                            type="button"
                            class="btn {{ $estaClasificado ? 'btn-outline-dark' : 'btn-dark' }} btn-clasificar-evidencia"
                            data-tipo="capacitacion"
                            data-id="{{ $capacitacion->id_capacitacion_fepade }}"
                            data-titulo="{{ $capacitacion->curso_nombre ?: 'Capacitación FEPADE' }}"
                            data-subtitulo="{{ $capacitacion->cliente ?? '' }}"
                        >
                            <i class="fa-solid {{ $estaClasificado ? 'fa-plus' : 'fa-tags' }} me-1"></i>

                            {{ $estaClasificado
                                ? 'Agregar otra clasificación'
                                : 'Clasificar evidencia'
                            }}
                        </button>

                    </div>

                </div>

            </div>

        @empty

            <div class="text-center py-4 text-muted">

                <i class="fa-regular fa-folder-open fs-2 mb-2"></i>

                <div class="fw-semibold">
                    No hay capacitaciones FEPADE disponibles.
                </div>

                <div class="small">
                    Las capacitaciones registradas por FEPADE aparecerán aquí automáticamente.
                </div>

            </div>

        @endforelse

    </div>
</div>


{{-- ============================================================
     ASISTENTE DE CLASIFICACIÓN
============================================================ --}}
<div
    class="card shadow-sm mb-4 d-none asistente-clasificacion"
    id="asistenteClasificacion"
>

    <div class="asistente-clasificacion-header">

        <div class="d-flex justify-content-between align-items-start gap-3">

            <div class="d-flex gap-3">

                <div class="asistente-header-icono">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>

                <div>
                    <div class="asistente-etiqueta">
                        CLASIFICACIÓN DE EVIDENCIA
                    </div>

                    <h4 class="mb-1 text-white" id="tituloAsistenteClasificacion">
                        Clasificar evidencia
                    </h4>

                    <div class="asistente-subtitulo">
                        Relaciona esta evidencia con un área de especialización y sus habilidades técnicas.
                    </div>
                </div>

            </div>

            <button
                type="button"
                class="btn-close btn-close-white"
                id="cerrarAsistente"
                aria-label="Cerrar"
            ></button>

        </div>

    </div>


    <div class="card-body p-4">

        {{-- EVIDENCIA + PASOS --}}
        <div class="asistente-contexto mb-4">

            <div class="row align-items-center g-4">

                <div class="col-lg-5">

                    <div class="d-flex gap-3 align-items-center">

                        <div class="evidencia-seleccionada-icono">
                            <i
                                class="fa-solid fa-file-lines"
                                id="iconoEvidenciaSeleccionada"
                            ></i>
                        </div>

                        <div>
                            <div class="small text-muted mb-1">
                                Evidencia seleccionada
                            </div>

                            <div
                                class="fw-bold"
                                id="tituloEvidenciaSeleccionada"
                            ></div>

                            <div
                                class="small text-muted"
                                id="subtituloEvidenciaSeleccionada"
                            ></div>
                        </div>

                    </div>

                </div>


                <div class="col-lg-7">

                    <div class="clasificacion-pasos">

                        <div
                            class="clasificacion-paso activo"
                            data-paso-indicador="1"
                        >
                            <div class="clasificacion-paso-numero">1</div>

                            <div>
                                <div class="fw-semibold">Evidencia</div>
                                <div class="small text-muted">Confirmar</div>
                            </div>
                        </div>

                        <div class="clasificacion-paso-linea"></div>

                        <div
                            class="clasificacion-paso"
                            data-paso-indicador="2"
                        >
                            <div class="clasificacion-paso-numero">2</div>

                            <div>
                                <div class="fw-semibold">Área</div>
                                <div class="small text-muted">Especialización</div>
                            </div>
                        </div>

                        <div class="clasificacion-paso-linea"></div>

                        <div
                            class="clasificacion-paso"
                            data-paso-indicador="3"
                        >
                            <div class="clasificacion-paso-numero">3</div>

                            <div>
                                <div class="fw-semibold">Habilidades</div>
                                <div class="small text-muted">Seleccionar</div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <form
            method="POST"
            action="{{ route('fac.consultores.habilidades.update', $consultor) }}"
            id="formClasificacion"
        >
            @csrf

            <input
                type="hidden"
                name="id_consultor_area"
                id="id_consultor_area"
                value="{{ old('id_consultor_area') }}"
            >

            <input
                type="hidden"
                name="id_atestado"
                id="id_atestado"
                value="{{ old('id_atestado') }}"
            >

            <input
                type="hidden"
                name="id_capacitacion_fepade"
                id="id_capacitacion_fepade"
                value="{{ old('id_capacitacion_fepade') }}"
            >

            {{-- El área continúa enviándose igual al backend --}}
            <input
                type="hidden"
                name="id_area_especializacion"
                id="id_area_especializacion"
                value="{{ old('id_area_especializacion') }}"
            >


            {{-- ====================================================
                 PASO 1
            ==================================================== --}}
            <div class="paso-contenido" data-paso="1">

                <div class="paso-titulo">

                    <div class="paso-titulo-numero">
                        1
                    </div>

                    <div>
                        <h5 class="mb-1">
                            Confirma la evidencia
                        </h5>

                        <p class="text-muted mb-0">
                            Esta será la evidencia que respaldará la clasificación que estás por registrar.
                        </p>
                    </div>

                </div>


                <div class="alert alert-primary border-0 mt-4 mb-0">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    En el siguiente paso selecciona el área de especialización que mejor representa los conocimientos respaldados por esta evidencia.
                </div>


                <div class="d-flex justify-content-end mt-4">

                    <button
                        type="button"
                        class="btn btn-dark btn-siguiente"
                        data-siguiente="2"
                    >
                        Seleccionar área
                        <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>

                </div>

            </div>


            {{-- ====================================================
                 PASO 2 - ÁREAS
            ==================================================== --}}
            <div class="paso-contenido d-none" data-paso="2">

                <div class="paso-titulo mb-4">

                    <div class="paso-titulo-numero">
                        2
                    </div>

                    <div class="flex-grow-1">

                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">

                            <div>
                                <h5 class="mb-1">
                                    Selecciona un área de especialización
                                </h5>

                                <p class="text-muted mb-0">
                                    Elige el área que mejor representa los conocimientos respaldados por esta evidencia.
                                </p>
                            </div>

                            <span
                                class="badge rounded-pill text-bg-light border px-3 py-2"
                                id="contadorAreas"
                            >
                                {{ $catalogos['areasEspecializacion']->count() }}
                                áreas disponibles
                            </span>

                        </div>

                    </div>

                </div>


                <div class="input-group mb-3">

                    <span class="input-group-text bg-white">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>

                    <input
                        type="search"
                        class="form-control"
                        id="buscarAreaEspecializacion"
                        placeholder="Buscar área de especialización..."
                        autocomplete="off"
                    >

                    <button
                        class="btn btn-outline-secondary d-none"
                        type="button"
                        id="limpiarBusquedaAreas"
                        title="Limpiar búsqueda"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                </div>


                <div
                    class="alert alert-light border d-none"
                    id="mensajeAreasSinResultados"
                >
                    <i class="fa-solid fa-magnifying-glass me-2"></i>
                    No se encontraron áreas de especialización que coincidan con la búsqueda.
                </div>


                <div class="area-grid" id="areasEspecializacionGrid">

                    @foreach ($catalogos['areasEspecializacion'] as $area)

                        <button
                            type="button"
                            class="area-opcion"
                            data-area-id="{{ $area->id_area_especializacion }}"
                            data-area-nombre="{{ $area->nombre }}"
                        >
                            <span class="area-opcion-icono">
                                <i class="fa-solid fa-layer-group"></i>
                            </span>

                            <span class="area-opcion-texto">
                                {{ $area->nombre }}
                            </span>

                            <span class="area-opcion-check">
                                <i class="fa-solid fa-check"></i>
                            </span>
                        </button>

                    @endforeach

                </div>


                @error('id_area_especializacion')
                    <div class="text-danger small mt-2">
                        {{ $message }}
                    </div>
                @enderror


                <div
                    class="alert alert-warning d-none mt-3"
                    id="mensajeAreaRequerida"
                >
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    Debes seleccionar un área de especialización para continuar.
                </div>


                <div class="d-flex justify-content-between mt-4">

                    <button
                        type="button"
                        class="btn btn-outline-secondary btn-anterior"
                        data-anterior="1"
                    >
                        <i class="fa-solid fa-arrow-left me-1"></i>
                        Anterior
                    </button>

                    <button
                        type="button"
                        class="btn btn-dark btn-siguiente"
                        data-siguiente="3"
                    >
                        Continuar
                        <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>

                </div>

            </div>


            {{-- ====================================================
                 PASO 3 - HABILIDADES
            ==================================================== --}}
            <div class="paso-contenido d-none" data-paso="3">

                <div class="paso-titulo mb-4">

                    <div class="paso-titulo-numero">
                        3
                    </div>

                    <div class="flex-grow-1">

                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">

                            <div>
                                <h5 class="mb-1">
                                    Selecciona las habilidades técnicas
                                </h5>

                                <p class="text-muted mb-0">
                                    Puedes seleccionar una o varias habilidades respaldadas por esta evidencia.
                                </p>
                            </div>

                            <span
                                class="badge rounded-pill text-bg-light border px-3 py-2"
                                id="contadorHabilidades"
                            >
                                0 seleccionadas · 0 disponibles
                            </span>

                        </div>

                    </div>

                </div>


                <div class="area-seleccionada-resumen mb-3">

                    <div class="small text-muted mb-1">
                        Área seleccionada
                    </div>

                    <div class="fw-semibold">
                        <i class="fa-solid fa-layer-group text-primary me-2"></i>
                        <span id="nombreAreaSeleccionada">
                            —
                        </span>
                    </div>

                </div>


                <div class="input-group mb-3">

                    <span class="input-group-text bg-white">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>

                    <input
                        type="search"
                        class="form-control"
                        id="buscarHabilidadTecnica"
                        placeholder="Buscar habilidad técnica..."
                        autocomplete="off"
                    >

                    <button
                        class="btn btn-outline-secondary d-none"
                        type="button"
                        id="limpiarBusquedaHabilidades"
                        title="Limpiar búsqueda"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                </div>


                <div
                    class="alert alert-warning d-none"
                    id="mensajeSinHabilidades"
                >
                    No hay habilidades técnicas disponibles para el área seleccionada.
                </div>


                <div
                    class="alert alert-light border d-none"
                    id="mensajeBusquedaSinResultados"
                >
                    <i class="fa-solid fa-magnifying-glass me-2"></i>
                    No se encontraron habilidades que coincidan con la búsqueda.
                </div>


                <div
                    class="habilidad-grid"
                    id="habilidadesTecnicasGrid"
                >

                    @foreach ($catalogos['habilidadesTecnicas'] as $habilidad)

                        @php
                            $seleccionada = in_array(
                                (string) $habilidad->id_habilidad_tecnica,
                                array_map(
                                    'strval',
                                    old('habilidades_tecnicas', [])
                                ),
                                true
                            );
                        @endphp

                        <label
                            class="habilidad-opcion"
                            data-area="{{ $habilidad->id_area_especializacion }}"
                            data-nombre="{{ $habilidad->nombre }}"
                            style="display: none;"
                        >

                            <input
                                class="habilidad-checkbox"
                                type="checkbox"
                                name="habilidades_tecnicas[]"
                                value="{{ $habilidad->id_habilidad_tecnica }}"
                                @checked($seleccionada)
                            >

                            <span class="habilidad-opcion-icono">
                                <i class="fa-solid fa-screwdriver-wrench"></i>
                            </span>

                            <span class="habilidad-opcion-texto">
                                {{ $habilidad->nombre }}
                            </span>

                            <span class="habilidad-opcion-check">
                                <i class="fa-solid fa-check"></i>
                            </span>

                        </label>

                    @endforeach

                </div>


                @error('habilidades_tecnicas')
                    <div class="text-danger small mt-2">
                        {{ $message }}
                    </div>
                @enderror

                @error('habilidades_tecnicas.*')
                    <div class="text-danger small mt-2">
                        {{ $message }}
                    </div>
                @enderror


                <div
                    class="alert alert-warning d-none mt-3"
                    id="mensajeHabilidadRequerida"
                >
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    Selecciona al menos una habilidad técnica.
                </div>


                <div class="d-flex justify-content-between mt-4">

                    <button
                        type="button"
                        class="btn btn-outline-secondary btn-anterior"
                        data-anterior="2"
                    >
                        <i class="fa-solid fa-arrow-left me-1"></i>
                        Anterior
                    </button>

                    <button
                        type="submit"
                        class="btn btn-success"
                        id="btnGuardarClasificacion"
                    >
                        <i class="fa-solid fa-check me-1"></i>
                        <span id="textoGuardarClasificacion">
                            Guardar clasificación
                        </span>
                    </button>

                </div>

            </div>

        </form>

    </div>
</div>


{{-- ============================================================
     NAVEGACIÓN DEL WIZARD
============================================================ --}}
@include(
    'fac.consultores.partials._wizard_actions',
    [
        'consultor' => $consultor,

        'anterior' => route(
            'fac.consultores.formacion.edit',
            $consultor
        ),

        'continuarRoute' => route(
            'fac.consultores.habilidades.continuar',
            $consultor
        ),

        'continuarLabel' => 'Continuar a Idiomas',
    ]
)

@endsection


@push('styles')
<style>
    /* ==========================================================
       EVIDENCIAS
    ========================================================== */

    .evidencia-card {
        transition:
            box-shadow .2s ease,
            border-color .2s ease;
        border-left-width: 4px !important;
    }

    .evidencia-card:hover {
        box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .06);
    }

    .evidencia-clasificada {
        border-left-color: var(--bs-success) !important;
    }

    .evidencia-pendiente {
        border-left-color: var(--bs-warning) !important;
    }

    .evidencia-icono {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--bs-light);
        color: var(--bs-primary);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .clasificacion-resumen {
        padding: .75rem 1rem;
        background: var(--bs-light);
        border-radius: .6rem;
        margin-bottom: .5rem;
    }

    .clasificacion-resumen:last-child {
        margin-bottom: 0;
    }


    /* ==========================================================
       ASISTENTE
    ========================================================== */

    .asistente-clasificacion {
        border: 2px solid #0d6efd !important;
        overflow: hidden;
    }

    .asistente-clasificacion-header {
        background:
            linear-gradient(
                135deg,
                #0b1d2e 0%,
                #102d49 100%
            );
        padding: 1.25rem 1.5rem;
    }

    .asistente-header-icono {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(255, 255, 255, .12);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .asistente-etiqueta {
        font-size: .72rem;
        letter-spacing: .08em;
        font-weight: 700;
        color: #7fbdff;
        margin-bottom: .2rem;
    }

    .asistente-subtitulo {
        color: rgba(255, 255, 255, .72);
        font-size: .9rem;
    }

    .asistente-contexto {
        background: #f4f8fc;
        border: 1px solid #dce8f4;
        border-radius: .8rem;
        padding: 1rem 1.25rem;
    }

    .evidencia-seleccionada-icono {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #fff;
        border: 1px solid #dce8f4;
        color: var(--bs-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }


    /* ==========================================================
       INDICADOR DE PASOS
    ========================================================== */

    .clasificacion-pasos {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .clasificacion-paso {
        display: flex;
        align-items: center;
        gap: .6rem;
        opacity: .45;
        transition: opacity .2s ease;
    }

    .clasificacion-paso.activo,
    .clasificacion-paso.completado {
        opacity: 1;
    }

    .clasificacion-paso-numero {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 2px solid var(--bs-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }

    .clasificacion-paso.activo .clasificacion-paso-numero {
        border-color: var(--bs-primary);
        background: var(--bs-primary);
        color: #fff;
    }

    .clasificacion-paso.completado .clasificacion-paso-numero {
        border-color: var(--bs-success);
        background: var(--bs-success);
        color: #fff;
    }

    .clasificacion-paso-linea {
        height: 2px;
        background: var(--bs-border-color);
        flex: 1;
        max-width: 55px;
        min-width: 25px;
        margin: 0 .7rem;
    }


    /* ==========================================================
       TÍTULOS DE PASO
    ========================================================== */

    .paso-titulo {
        display: flex;
        align-items: flex-start;
        gap: .85rem;
    }

    .paso-titulo-numero {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--bs-primary);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }


    /* ==========================================================
       ÁREAS
    ========================================================== */

    .area-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .75rem;
    }

    .area-opcion {
        position: relative;
        min-height: 76px;
        width: 100%;
        display: flex;
        align-items: center;
        gap: .8rem;
        text-align: left;
        border: 1px solid var(--bs-border-color);
        border-radius: .7rem;
        background: #fff;
        padding: .85rem 2.6rem .85rem .9rem;
        color: var(--bs-body-color);
        transition:
            border-color .15s ease,
            background-color .15s ease,
            box-shadow .15s ease,
            transform .15s ease;
    }

    .area-opcion:hover {
        border-color: var(--bs-primary);
        background: rgba(var(--bs-primary-rgb), .025);
        box-shadow: 0 .2rem .55rem rgba(0, 0, 0, .05);
        transform: translateY(-1px);
    }

    .area-opcion.seleccionada {
        border: 2px solid var(--bs-primary);
        background: rgba(var(--bs-primary-rgb), .07);
        padding:
            calc(.85rem - 1px)
            calc(2.6rem - 1px)
            calc(.85rem - 1px)
            calc(.9rem - 1px);
    }

    .area-opcion-icono {
        width: 38px;
        height: 38px;
        border-radius: 9px;
        background: var(--bs-light);
        color: var(--bs-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .area-opcion.seleccionada .area-opcion-icono {
        background: var(--bs-primary);
        color: #fff;
    }

    .area-opcion-texto {
        font-size: .92rem;
        line-height: 1.25rem;
        font-weight: 500;
    }

    .area-opcion-check {
        position: absolute;
        top: .55rem;
        right: .55rem;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: var(--bs-primary);
        color: #fff;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: .7rem;
    }

    .area-opcion.seleccionada .area-opcion-check {
        display: flex;
    }


    /* ==========================================================
       HABILIDADES
    ========================================================== */

    .area-seleccionada-resumen {
        padding: .8rem 1rem;
        border-radius: .65rem;
        background: #f4f8fc;
        border: 1px solid #dce8f4;
    }

    .habilidad-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
    }

    .habilidad-opcion {
        position: relative;
        min-height: 58px;
        display: flex;
        align-items: center;
        gap: .75rem;
        border: 1px solid var(--bs-border-color);
        border-radius: .65rem;
        padding: .75rem 2.5rem .75rem .85rem;
        cursor: pointer;
        background: #fff;
        transition:
            border-color .15s ease,
            background-color .15s ease,
            box-shadow .15s ease,
            transform .15s ease;
    }

    .habilidad-opcion:hover {
        background: rgba(var(--bs-primary-rgb), .025);
        border-color: var(--bs-primary);
        box-shadow: 0 .2rem .5rem rgba(0, 0, 0, .04);
        transform: translateY(-1px);
    }

    /*
     * El checkbox continúa existiendo y enviándose al backend,
     * pero ya no se muestra visualmente.
     */
    .habilidad-checkbox {
        position: absolute !important;
        opacity: 0 !important;
        pointer-events: none !important;
        width: 1px !important;
        height: 1px !important;
        margin: 0 !important;
    }

    .habilidad-opcion-icono {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: var(--bs-light);
        color: var(--bs-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .habilidad-opcion-texto {
        font-size: .92rem;
        line-height: 1.2rem;
    }

    .habilidad-opcion-check {
        position: absolute;
        top: 50%;
        right: .75rem;
        transform: translateY(-50%);
        width: 23px;
        height: 23px;
        border-radius: 50%;
        background: var(--bs-primary);
        color: #fff;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: .7rem;
    }

    .habilidad-opcion:has(.habilidad-checkbox:checked) {
        border: 2px solid var(--bs-primary);
        background: rgba(var(--bs-primary-rgb), .07);
        padding:
            calc(.75rem - 1px)
            calc(2.5rem - 1px)
            calc(.75rem - 1px)
            calc(.85rem - 1px);
    }

    .habilidad-opcion:has(.habilidad-checkbox:checked)
    .habilidad-opcion-icono {
        background: var(--bs-primary);
        color: #fff;
    }

    .habilidad-opcion:has(.habilidad-checkbox:checked)
    .habilidad-opcion-check {
        display: flex;
    }


    /* ==========================================================
       RESPONSIVE
    ========================================================== */

    @media (max-width: 1199.98px) {
        .area-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {

        .area-grid,
        .habilidad-grid {
            grid-template-columns: 1fr;
        }

        .clasificacion-pasos {
            justify-content: center;
            margin-top: 1rem;
        }

        .clasificacion-paso {
            flex-direction: column;
            text-align: center;
            gap: .25rem;
        }

        .clasificacion-paso-linea {
            margin: 18px .35rem 0;
        }

        .clasificacion-paso .small {
            display: none;
        }

        .asistente-clasificacion-header {
            padding: 1rem;
        }
    }
</style>
@endpush


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const asistente = document.getElementById('asistenteClasificacion');
    const form = document.getElementById('formClasificacion');

    const idAtestado = document.getElementById('id_atestado');
    const idCapacitacion = document.getElementById('id_capacitacion_fepade');
    const areaInput = document.getElementById('id_area_especializacion');

    const idConsultorArea = document.getElementById('id_consultor_area');

    const tituloAsistenteClasificacion =
        document.getElementById('tituloAsistenteClasificacion');

    const textoGuardarClasificacion =
        document.getElementById('textoGuardarClasificacion');

    const tituloEvidencia = document.getElementById('tituloEvidenciaSeleccionada');
    const subtituloEvidencia = document.getElementById('subtituloEvidenciaSeleccionada');
    const iconoEvidencia = document.getElementById('iconoEvidenciaSeleccionada');

    const areaItems = Array.from(
        document.querySelectorAll('.area-opcion')
    );

    const habilidadItems = Array.from(
        document.querySelectorAll('.habilidad-opcion')
    );

    const buscarArea = document.getElementById('buscarAreaEspecializacion');
    const limpiarBusquedaAreas = document.getElementById('limpiarBusquedaAreas');
    const mensajeAreasSinResultados = document.getElementById('mensajeAreasSinResultados');
    const contadorAreas = document.getElementById('contadorAreas');

    const buscarHabilidad = document.getElementById('buscarHabilidadTecnica');
    const limpiarBusquedaHabilidades = document.getElementById('limpiarBusquedaHabilidades');

    const mensajeSinHabilidades = document.getElementById('mensajeSinHabilidades');
    const mensajeBusquedaSinResultados = document.getElementById('mensajeBusquedaSinResultados');

    const mensajeAreaRequerida = document.getElementById('mensajeAreaRequerida');
    const mensajeHabilidadRequerida = document.getElementById('mensajeHabilidadRequerida');

    const contadorHabilidades = document.getElementById('contadorHabilidades');
    const nombreAreaSeleccionada = document.getElementById('nombreAreaSeleccionada');


    function normalizarTexto(texto) {
        return (texto || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();
    }


    function irAPaso(numero) {

        document.querySelectorAll('.paso-contenido').forEach(paso => {
            paso.classList.toggle(
                'd-none',
                Number(paso.dataset.paso) !== Number(numero)
            );
        });

        document.querySelectorAll('[data-paso-indicador]').forEach(indicador => {

            const pasoIndicador = Number(indicador.dataset.pasoIndicador);

            indicador.classList.remove('activo', 'completado');

            if (pasoIndicador < numero) {
                indicador.classList.add('completado');
            }

            if (pasoIndicador === Number(numero)) {
                indicador.classList.add('activo');
            }

        });

    }


    function seleccionarArea(idArea) {

        areaInput.value = idArea || '';

        areaItems.forEach(item => {

            const seleccionada =
                String(item.dataset.areaId) === String(idArea);

            item.classList.toggle(
                'seleccionada',
                seleccionada
            );

        });

        const areaSeleccionada = areaItems.find(item =>
            String(item.dataset.areaId) === String(idArea)
        );

        nombreAreaSeleccionada.textContent =
            areaSeleccionada
                ? areaSeleccionada.dataset.areaNombre
                : '—';

        mensajeAreaRequerida.classList.add('d-none');

        /*
         * Si cambia el área, no podemos conservar habilidades
         * pertenecientes a otra área.
         */
        habilidadItems.forEach(item => {

            const pertenece =
                String(item.dataset.area) === String(idArea);

            const checkbox =
                item.querySelector('.habilidad-checkbox');

            if (!pertenece && checkbox) {
                checkbox.checked = false;
            }

        });

        buscarHabilidad.value = '';
        limpiarBusquedaHabilidades.classList.add('d-none');
        mensajeBusquedaSinResultados.classList.add('d-none');

        actualizarHabilidades();
    }


    function limpiarFormulario() {

        form.reset();

        idConsultorArea.value = '';

        tituloAsistenteClasificacion.textContent =
            'Clasificar evidencia';

        textoGuardarClasificacion.textContent =
            'Guardar clasificación';

        idAtestado.value = '';
        idCapacitacion.value = '';

        seleccionarArea('');

        buscarArea.value = '';
        buscarHabilidad.value = '';

        limpiarBusquedaAreas.classList.add('d-none');
        limpiarBusquedaHabilidades.classList.add('d-none');

        mensajeAreasSinResultados.classList.add('d-none');
        mensajeBusquedaSinResultados.classList.add('d-none');
        mensajeAreaRequerida.classList.add('d-none');
        mensajeHabilidadRequerida.classList.add('d-none');

        habilidadItems.forEach(item => {

            const checkbox =
                item.querySelector('.habilidad-checkbox');

            if (checkbox) {
                checkbox.checked = false;
            }

            item.style.display = 'none';

        });

        filtrarAreas();
        actualizarHabilidades();

        irAPaso(1);
    }


    function abrirAsistente(boton) {

        limpiarFormulario();

        const tipo = boton.dataset.tipo;
        const id = boton.dataset.id;
        const titulo = boton.dataset.titulo || 'Evidencia';
        const subtitulo = boton.dataset.subtitulo || '';

        if (tipo === 'atestado') {

            idAtestado.value = id;
            idCapacitacion.value = '';

            iconoEvidencia.className =
                'fa-solid fa-file-lines';

        } else {

            idAtestado.value = '';
            idCapacitacion.value = id;

            iconoEvidencia.className =
                'fa-solid fa-chalkboard-user';

        }

        tituloEvidencia.textContent = titulo;
        subtituloEvidencia.textContent = subtitulo;

        asistente.classList.remove('d-none');

        irAPaso(1);

        asistente.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });

    }

    function editarClasificacion(boton) {

        limpiarFormulario();

        const clasificacionId =
            boton.dataset.clasificacionId;

        const tipo =
            boton.dataset.tipo;

        const id =
            boton.dataset.id;

        const titulo =
            boton.dataset.titulo || 'Evidencia';

        const subtitulo =
            boton.dataset.subtitulo || '';

        const area =
            boton.dataset.area;

        let habilidades = [];

        try {
            habilidades =
                JSON.parse(boton.dataset.habilidades || '[]')
                    .map(String);
        } catch (error) {
            habilidades = [];
        }

        idConsultorArea.value =
            clasificacionId;

        tituloAsistenteClasificacion.textContent =
            'Editar clasificación';

        textoGuardarClasificacion.textContent =
            'Guardar cambios';


        if (tipo === 'atestado') {

            idAtestado.value = id;
            idCapacitacion.value = '';

            iconoEvidencia.className =
                'fa-solid fa-file-lines';

        } else {

            idAtestado.value = '';
            idCapacitacion.value = id;

            iconoEvidencia.className =
                'fa-solid fa-chalkboard-user';

        }


        tituloEvidencia.textContent =
            titulo;

        subtituloEvidencia.textContent =
            subtitulo;


        /*
        * Primero seleccionamos el área.
        * Esto prepara únicamente las habilidades
        * pertenecientes a dicha área.
        */
        seleccionarArea(area);


        /*
        * Después marcamos las habilidades
        * que ya pertenecen a esta clasificación.
        */
        habilidadItems.forEach(item => {

            const checkbox =
                item.querySelector('.habilidad-checkbox');

            if (!checkbox) {
                return;
            }

            checkbox.checked =
                String(item.dataset.area) === String(area) &&
                habilidades.includes(String(checkbox.value));

        });


        actualizarHabilidades();


        asistente.classList.remove('d-none');

        /*
        * En edición llevamos directamente al área.
        * El usuario puede cambiar área y luego continuar
        * a habilidades.
        */
        irAPaso(2);


        asistente.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    function cerrarAsistente() {

        asistente.classList.add('d-none');

        limpiarFormulario();

    }


    /* ==========================================================
       ÁREAS
    ========================================================== */

    function filtrarAreas() {

        const termino =
            normalizarTexto(buscarArea.value);

        let visibles = 0;

        areaItems.forEach(item => {

            const nombre =
                normalizarTexto(item.dataset.areaNombre);

            const coincide =
                !termino || nombre.includes(termino);

            item.style.display =
                coincide ? '' : 'none';

            if (coincide) {
                visibles++;
            }

        });

        limpiarBusquedaAreas.classList.toggle(
            'd-none',
            !termino
        );

        mensajeAreasSinResultados.classList.toggle(
            'd-none',
            !termino || visibles > 0
        );

        contadorAreas.textContent =
            `${visibles} área${visibles === 1 ? '' : 's'} disponible${visibles === 1 ? '' : 's'}`;
    }


    areaItems.forEach(item => {

        item.addEventListener('click', function () {

            seleccionarArea(
                this.dataset.areaId
            );

        });

    });


    buscarArea.addEventListener(
        'input',
        filtrarAreas
    );


    limpiarBusquedaAreas.addEventListener('click', function () {

        buscarArea.value = '';

        filtrarAreas();

        buscarArea.focus();

    });


    /* ==========================================================
       HABILIDADES
    ========================================================== */

    function obtenerHabilidadesArea() {

        const area = areaInput.value;

        return habilidadItems.filter(item => {
            return String(item.dataset.area) === String(area);
        });

    }


    function actualizarContadorHabilidades() {

        const habilidadesArea =
            obtenerHabilidadesArea();

        const seleccionadas =
            habilidadesArea.filter(item => {

                const checkbox =
                    item.querySelector('.habilidad-checkbox');

                return checkbox && checkbox.checked;

            }).length;

        const total =
            habilidadesArea.length;

        contadorHabilidades.textContent =
            `${seleccionadas} seleccionada${seleccionadas === 1 ? '' : 's'} · ` +
            `${total} disponible${total === 1 ? '' : 's'}`;

    }


    function filtrarHabilidades() {

        const area = areaInput.value;

        const termino =
            normalizarTexto(buscarHabilidad.value);

        let totalArea = 0;
        let visibles = 0;

        habilidadItems.forEach(item => {

            const pertenece =
                String(item.dataset.area) === String(area);

            if (!pertenece) {

                item.style.display = 'none';

                return;
            }

            totalArea++;

            const nombre =
                normalizarTexto(item.dataset.nombre);

            const coincide =
                !termino || nombre.includes(termino);

            item.style.display =
                coincide ? '' : 'none';

            if (coincide) {
                visibles++;
            }

        });

        limpiarBusquedaHabilidades.classList.toggle(
            'd-none',
            !termino
        );

        mensajeBusquedaSinResultados.classList.toggle(
            'd-none',
            !area ||
            !termino ||
            totalArea === 0 ||
            visibles > 0
        );

        actualizarContadorHabilidades();

    }


    function actualizarHabilidades() {

        const area = areaInput.value;

        const habilidadesArea =
            obtenerHabilidadesArea();

        const tieneHabilidades =
            habilidadesArea.length > 0;

        mensajeSinHabilidades.classList.toggle(
            'd-none',
            !area || tieneHabilidades
        );

        filtrarHabilidades();

    }


    habilidadItems.forEach(item => {

        const checkbox =
            item.querySelector('.habilidad-checkbox');

        checkbox?.addEventListener('change', function () {

            mensajeHabilidadRequerida.classList.add('d-none');

            actualizarContadorHabilidades();

        });

    });


    buscarHabilidad.addEventListener(
        'input',
        filtrarHabilidades
    );


    limpiarBusquedaHabilidades.addEventListener('click', function () {

        buscarHabilidad.value = '';

        filtrarHabilidades();

        buscarHabilidad.focus();

    });


    /* ==========================================================
       ASISTENTE
    ========================================================== */

    document.querySelectorAll('.btn-clasificar-evidencia').forEach(boton => {

        boton.addEventListener('click', function () {
            abrirAsistente(this);
        });

    });

    document.querySelectorAll('.btn-editar-clasificacion').forEach(boton => {

        boton.addEventListener('click', function () {
            editarClasificacion(this);
        });

    });


    document.getElementById('cerrarAsistente')
        ?.addEventListener(
            'click',
            cerrarAsistente
        );


    document.querySelectorAll('.btn-siguiente').forEach(boton => {

        boton.addEventListener('click', function () {

            const siguiente =
                Number(this.dataset.siguiente);

            if (siguiente === 3 && !areaInput.value) {

                mensajeAreaRequerida.classList.remove('d-none');

                return;
            }

            mensajeAreaRequerida.classList.add('d-none');

            irAPaso(siguiente);

        });

    });


    document.querySelectorAll('.btn-anterior').forEach(boton => {

        boton.addEventListener('click', function () {

            irAPaso(
                Number(this.dataset.anterior)
            );

        });

    });


    /* ==========================================================
       VALIDACIÓN FINAL
    ========================================================== */

    form.addEventListener('submit', function (event) {

        const seleccionadas =
            form.querySelectorAll(
                '.habilidad-checkbox:checked'
            );

        if (!areaInput.value) {

            event.preventDefault();

            mensajeAreaRequerida.classList.remove('d-none');

            irAPaso(2);

            return;
        }

        if (seleccionadas.length === 0) {

            event.preventDefault();

            mensajeHabilidadRequerida.classList.remove('d-none');

            irAPaso(3);

            return;
        }

    });


    /* ==========================================================
       RECUPERACIÓN DE OLD() POR ERROR DE VALIDACIÓN
    ========================================================== */

    const oldAtestado =
        @json(old('id_atestado'));

    const oldCapacitacion =
        @json(old('id_capacitacion_fepade'));

    const oldArea =
        @json(old('id_area_especializacion'));

    const oldConsultorArea =
        @json(old('id_consultor_area'));

    const tieneErrores =
        @json($errors->any());


    if (
        tieneErrores &&
        (oldAtestado || oldCapacitacion)
    ) {

        asistente.classList.remove('d-none');

        idConsultorArea.value =
            oldConsultorArea || '';

        if (oldConsultorArea) {

            tituloAsistenteClasificacion.textContent =
                'Editar clasificación';

            textoGuardarClasificacion.textContent =
                'Guardar cambios';

        } else {

            tituloAsistenteClasificacion.textContent =
                'Clasificar evidencia';

            textoGuardarClasificacion.textContent =
                'Guardar clasificación';

        }

        const botonOriginal =
            document.querySelector(
                oldAtestado
                    ? `.btn-clasificar-evidencia[data-tipo="atestado"][data-id="${oldAtestado}"]`
                    : `.btn-clasificar-evidencia[data-tipo="capacitacion"][data-id="${oldCapacitacion}"]`
            );

        if (botonOriginal) {

            tituloEvidencia.textContent =
                botonOriginal.dataset.titulo || 'Evidencia';

            subtituloEvidencia.textContent =
                botonOriginal.dataset.subtitulo || '';

            iconoEvidencia.className =
                oldAtestado
                    ? 'fa-solid fa-file-lines'
                    : 'fa-solid fa-chalkboard-user';

        }

        if (oldArea) {
            seleccionarArea(oldArea);
        }

        actualizarHabilidades();

        irAPaso(3);

        asistente.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });

    } else {

        filtrarAreas();
        actualizarHabilidades();

    }

});
</script>
@endpush
