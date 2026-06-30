@extends('layouts.app')

@section('title', 'Expediente consultor | Facilitadores FEPADE')
@section('page-title', 'Expediente consultor')
@section('page-subtitle', 'Vista integral del perfil del consultor')

@section('content')

@php
    $tiposTelefono = $catalogos['tiposTelefono'] ?? collect();
    $tiposRedSocial = $catalogos['tiposRedSocial'] ?? collect();
    $tiposAtestado = $catalogos['tiposAtestado'] ?? collect();
    $tiposFormacion = $catalogos['tiposFormacion'] ?? collect();
    $nivelesAcademicos = $catalogos['nivelesAcademicos'] ?? collect();
    $paises = $catalogos['paises'] ?? collect();
    $tiposDocumento = $catalogos['tiposDocumento'] ?? collect();

    $tiposDisponibilidad = $catalogos['tiposDisponibilidad'] ?? collect();
    $areasCatalogo = $catalogos['areasEspecializacion'] ?? collect();
    $habilidadesTecnicasCatalogo = $catalogos['habilidadesTecnicas'] ?? collect();
    $idiomasCatalogo = $catalogos['idiomas'] ?? collect();
    $nivelesIdioma = $catalogos['nivelesIdioma'] ?? collect();
    $tiposReferencia = $catalogos['tiposReferencia'] ?? collect();
    $tiposRelacion = $catalogos['tiposRelacion'] ?? collect();

    $nombreCompleto = $consultor->nombre_completo ?? trim(($consultor->nombres ?? '') . ' ' . ($consultor->apellidos ?? ''));
    $iniciales = strtoupper(substr($consultor->nombres ?? 'C', 0, 1) . substr($consultor->apellidos ?? 'F', 0, 1));

    $correoPrincipal = $consultor->emails->firstWhere('principal', true) ?? $consultor->emails->first();
    $telefonoPrincipal = $consultor->telefonos->first();

    $documentoIdentificacion = $consultor->documentos->first(function ($documento) use ($tiposDocumento, $consultor) {
        $tipo = $tiposDocumento->get($documento->id_tipo_documento);
        return $tipo && strtolower($tipo->nombre) === strtolower($consultor->tipo_identificacion ?? '');
    });

    $documentoNit = $consultor->documentos->first(function ($documento) use ($tiposDocumento) {
        $tipo = $tiposDocumento->get($documento->id_tipo_documento);
        return $tipo && strtolower($tipo->nombre) === 'nit';
    });

    $documentoNrc = $consultor->documentos->first(function ($documento) use ($tiposDocumento) {
        $tipo = $tiposDocumento->get($documento->id_tipo_documento);
        return $tipo && strtolower($tipo->nombre) === 'nrc';
    });

    $documentosIdentificacionIds = collect([
        $documentoIdentificacion?->id_documento,
        $documentoNit?->id_documento,
        $documentoNrc?->id_documento,
    ])->filter()->values();

    $documentosGenerales = $consultor->documentos->reject(function ($documento) use ($documentosIdentificacionIds) {
        return $documentosIdentificacionIds->contains($documento->id_documento);
    });

    $experiencias = $consultor->experienciasLaborales ?? collect();
    $atestados = $consultor->atestados ?? collect();
    $capacitacionesFepade = $consultor->capacitacionesFepade ?? collect();
    $disponibilidades = $consultor->disponibilidades ?? collect();
    $areasPerfil = $consultor->areasEspecializacion ?? collect();
    $idiomas = $consultor->idiomas ?? collect();
    $referencias = $consultor->referencias ?? collect();

    $avancePerfil = $avancePerfil ?? $consultor->avancePerfil();
    $porcentajePerfil = $avancePerfil['porcentaje'] ?? 0;

    $claseAvance = match (true) {
        $porcentajePerfil >= 85 => 'bg-success',
        $porcentajePerfil >= 60 => 'bg-warning',
        default => 'bg-danger',
    };

    $textoAvance = match (true) {
        $porcentajePerfil >= 85 => 'Perfil avanzado',
        $porcentajePerfil >= 60 => 'Perfil en progreso',
        default => 'Perfil incompleto',
    };
@endphp

<x-ui.page-header title="Expediente del consultor" subtitle="Vista integral del perfil profesional registrado.">
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('fac.consultores.edit', $consultor) }}" class="btn btn-fepade">
            Editar datos personales
        </a>

        <a href="{{ route('fac.consultores.index') }}" class="btn btn-outline-secondary">
            Volver
        </a>
    </div>
</x-ui.page-header>

<div class="expediente-page">

    <section class="expediente-hero">
        <div class="expediente-hero-main">
            <div class="expediente-avatar-large">
                @if($consultor->ruta_foto)
                    <img 
                        src="{{ \Illuminate\Support\Facades\Storage::url($consultor->ruta_foto) }}" 
                        alt="Foto de {{ $nombreCompleto }}"
                    >
                @else
                    <span>{{ $iniciales }}</span>
                @endif
            </div>

            <div>
                <h2>{{ $nombreCompleto ?: 'Consultor sin nombre' }}</h2>

                <div class="expediente-meta">
                    <span>{{ $consultor->sexoCatalogo?->nombre ?? 'Sexo no registrado' }}</span>
                    <span>{{ $consultor->nacionalidad ?? 'Nacionalidad no registrada' }}</span>
                    <span>
                        {{ $consultor->fecha_nacimiento ? $consultor->fecha_nacimiento->format('d/m/Y') : 'Fecha nacimiento no registrada' }}
                    </span>
                </div>

                <div class="expediente-badges">
                    <span class="expediente-status-badge {{ $consultor->activo ? 'success' : 'danger' }}">
                        {{ $consultor->activo ? 'Activo' : 'Inactivo' }}
                    </span>

                    <span class="expediente-status-badge {{ $consultor->vigente ? 'success' : 'warning' }}">
                        {{ $consultor->vigente ? 'Vigente' : 'No vigente' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="expediente-hero-actions">
            <a href="{{ route('fac.consultores.edit', $consultor) }}" class="btn btn-light">
                Editar perfil
            </a>

            <a href="{{ route('fac.consultores.index') }}" class="btn btn-outline-light">
                Volver
            </a>
        </div>
    </section>

    <section class="fepade-card mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h4 class="mb-1">Completitud del perfil</h4>
                <p class="text-muted mb-0">
                    {{ $avancePerfil['obtenidos'] }} de {{ $avancePerfil['total'] }} criterios completados.
                </p>
            </div>

            <div class="text-end">
                <div class="display-6 fw-bold text-success">
                    {{ $porcentajePerfil }}%
                </div>
                <span class="badge bg-light text-dark border">
                    {{ $textoAvance }}
                </span>
            </div>
        </div>

        <div class="progress mt-3" style="height: 14px;">
            <div
                class="progress-bar {{ $claseAvance }}"
                role="progressbar"
                style="width: {{ $porcentajePerfil }}%;"
                aria-valuenow="{{ $porcentajePerfil }}"
                aria-valuemin="0"
                aria-valuemax="100"
            >
                {{ $porcentajePerfil }}%
            </div>
        </div>

        <div class="row g-2 mt-3">
            @foreach($avancePerfil['puntos_fijos'] as $criterio => $completo)
                <div class="col-md-6 col-lg-4">
                    <div class="d-flex align-items-center gap-2 small">
                        @if($completo)
                            <i class="fa-solid fa-circle-check text-success"></i>
                        @else
                            <i class="fa-regular fa-circle text-muted"></i>
                        @endif

                        <span class="{{ $completo ? 'text-dark' : 'text-muted' }}">
                            {{ $criterio }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        @if(!empty($avancePerfil['puntos_dinamicos']))
            <div class="mt-3">
                <button
                    class="btn btn-sm btn-outline-secondary"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#criteriosDinamicosPerfil"
                >
                    Ver criterios por atestado y capacitación FEPADE
                </button>

                <div class="collapse mt-3" id="criteriosDinamicosPerfil">
                    <div class="row g-2">
                        @foreach($avancePerfil['puntos_dinamicos'] as $criterio => $completo)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2 small">
                                    @if($completo)
                                        <i class="fa-solid fa-circle-check text-success"></i>
                                    @else
                                        <i class="fa-regular fa-circle text-muted"></i>
                                    @endif

                                    <span class="{{ $completo ? 'text-dark' : 'text-muted' }}">
                                        {{ $criterio }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </section>

    <section class="expediente-summary-grid">
        <div class="expediente-summary-card">
            <span>Correo principal</span>
            <strong>{{ $correoPrincipal->email ?? 'No registrado' }}</strong>
        </div>

        <div class="expediente-summary-card">
            <span>Teléfono</span>
            <strong>{{ $telefonoPrincipal->numero_telefono ?? 'No registrado' }}</strong>
        </div>

        <div class="expediente-summary-card">
            <span>Experiencias</span>
            <strong>{{ $experiencias->count() }}</strong>
        </div>

        <div class="expediente-summary-card">
            <span>Atestados</span>
            <strong>{{ $atestados->count() }}</strong>
        </div>

        <div class="expediente-summary-card">
            <span>Capacitaciones FEPADE</span>
            <strong>{{ $capacitacionesFepade->count() }}</strong>
        </div>

        <div class="expediente-summary-card">
            <span>Idiomas</span>
            <strong>{{ $idiomas->count() }}</strong>
        </div>

        <div class="expediente-summary-card">
            <span>Áreas de especialización</span>
            <strong>{{ $areasPerfil->count() }}</strong>
        </div>
    </section>

    <div class="expediente-layout">

        <main class="expediente-main">

            <section class="expediente-panel">
                <div class="expediente-panel-header">
                    <div>
                        <h4>Datos personales</h4>
                        <p>Información general, identificación y residencia.</p>
                    </div>

                    <a href="{{ route('fac.consultores.edit', $consultor) }}" class="btn btn-sm btn-outline-secondary">
                        Editar
                    </a>
                </div>

                <div class="expediente-info-grid">
                    <div class="expediente-info-item">
                        <span>Nombres</span>
                        <strong>{{ $consultor->nombres ?? 'No registrado' }}</strong>
                    </div>

                    <div class="expediente-info-item">
                        <span>Apellidos</span>
                        <strong>{{ $consultor->apellidos ?? 'No registrado' }}</strong>
                    </div>

                    <div class="expediente-info-item">
                        <span>Apellido de casa</span>
                        <strong>{{ $consultor->apellido_casa ?? 'No registrado' }}</strong>
                    </div>

                    <div class="expediente-info-item">
                        <span>Estado civil</span>
                        <strong>{{ $consultor->estado_civil ?? 'No registrado' }}</strong>
                    </div>

                    <div class="expediente-info-item">
                        <span>Nacionalidad</span>
                        <strong>{{ $consultor->nacionalidad ?? 'No registrada' }}</strong>
                    </div>

                    <div class="expediente-info-item">
                        <span>Sexo</span>
                        <strong>{{ $consultor->sexoCatalogo?->nombre ?? 'No registrado' }}</strong>
                    </div>

                    <div class="expediente-info-item">
                        <span>Tipo de identificación</span>
                        <strong>{{ $consultor->tipo_identificacion ?? 'No registrado' }}</strong>
                    </div>

                    <div class="expediente-info-item">
                        <span>Número de identificación</span>
                        <strong>{{ $consultor->numero_identificacion ?? 'No registrado' }}</strong>
                    </div>

                    <div class="expediente-info-item">
                        <span>NIT</span>
                        <strong>{{ $consultor->nit ?? 'No registrado' }}</strong>
                    </div>

                    <div class="expediente-info-item">
                        <span>NRC</span>
                        <strong>{{ $consultor->nrc ?? 'No registrado' }}</strong>
                    </div>

                    <div class="expediente-info-item">
                        <span>Fecha de nacimiento</span>
                        <strong>{{ $consultor->fecha_nacimiento ? $consultor->fecha_nacimiento->format('d/m/Y') : 'No registrada' }}</strong>
                    </div>

                    <div class="expediente-info-item">
                        <span>Vigencia</span>
                        <strong>{{ $consultor->vigente ? 'Vigente' : 'No vigente' }}</strong>
                    </div>

                    <div class="expediente-info-item wide">
                        <span>Dirección de residencia</span>
                        <strong>{{ $consultor->direccion_residencia ?? 'No registrada' }}</strong>
                    </div>
                </div>
            </section>

            <section class="expediente-panel">
                <div class="expediente-panel-header">
                    <div>
                        <h4>Documentos de identificación</h4>
                        <p>Archivos principales asociados a la identificación fiscal y personal.</p>
                    </div>

                    @if(\Illuminate\Support\Facades\Route::has('fac.consultores.documentos.edit'))
                        <a href="{{ route('fac.consultores.documentos.edit', $consultor) }}" class="btn btn-sm btn-outline-secondary">
                            Editar documentos
                        </a>
                    @endif
                </div>

                <div class="expediente-cards-grid">
                    <div class="expediente-mini-card">
                        <h5>{{ $consultor->tipo_identificacion ?? 'Documento de identificación' }}</h5>
                        <p>Número: {{ $consultor->numero_identificacion ?? 'No registrado' }}</p>

                        @if($documentoIdentificacion?->url_archivo)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($documentoIdentificacion->url_archivo) }}" target="_blank" class="expediente-file-link">
                                Ver documento
                            </a>
                        @else
                            <span>Sin documento adjunto</span>
                        @endif
                    </div>

                    <div class="expediente-mini-card">
                        <h5>NIT</h5>
                        <p>Número: {{ $consultor->nit ?? 'No registrado' }}</p>

                        @if($documentoNit?->url_archivo)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($documentoNit->url_archivo) }}" target="_blank" class="expediente-file-link">
                                Ver documento
                            </a>
                        @else
                            <span>Sin documento adjunto</span>
                        @endif
                    </div>

                    <div class="expediente-mini-card">
                        <h5>NRC</h5>
                        <p>Número: {{ $consultor->nrc ?? 'No registrado' }}</p>
                        <p>Actividad / giro: {{ $documentoNrc?->actividad_giro ?? 'No registrado' }}</p>

                        @if($documentoNrc?->url_archivo)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($documentoNrc->url_archivo) }}" target="_blank" class="expediente-file-link">
                                Ver documento
                            </a>
                        @else
                            <span>Sin documento adjunto</span>
                        @endif
                    </div>
                </div>
            </section>

            <section class="expediente-panel">
                <div class="expediente-panel-header">
                    <div>
                        <h4>Experiencia laboral</h4>
                        <p>Trayectoria profesional, cargos, empresas y evidencias.</p>
                    </div>

                    @if(\Illuminate\Support\Facades\Route::has('fac.consultores.experiencia.edit'))
                        <a href="{{ route('fac.consultores.experiencia.edit', $consultor) }}" class="btn btn-sm btn-outline-secondary">
                            Editar experiencia
                        </a>
                    @endif
                </div>

                @forelse($experiencias as $experiencia)
                    <article class="expediente-timeline-card">
                        <h5>{{ $experiencia->cargo ?? 'Cargo no registrado' }}</h5>
                        <p>{{ $experiencia->empresa ?? 'Empresa no registrada' }}</p>

                        <span class="expediente-date">
                            {{ $experiencia->desde ? $experiencia->desde->format('d/m/Y') : 'S/F' }}
                            -
                            {{ $experiencia->trabajo_actual ? 'Actualidad' : ($experiencia->hasta ? $experiencia->hasta->format('d/m/Y') : 'S/F') }}
                        </span>

                        @if($experiencia->trabajo_actual)
                            <div class="mt-2">
                                <span class="badge badge-success-soft">Trabajo actual</span>
                            </div>
                        @endif

                        @if($experiencia->descripcion)
                            <p class="expediente-description">{{ $experiencia->descripcion }}</p>
                        @endif

                        <div class="expediente-tag-row">
                            <span>Jefe: {{ $experiencia->jefe_nombre ?? 'No registrado' }}</span>
                            <span>{{ $experiencia->jefe_email ?? 'Sin correo' }}</span>
                            <span>{{ $experiencia->jefe_telefono ?? 'Sin teléfono' }}</span>
                        </div>

                        @if($experiencia->url_evidencia)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($experiencia->url_evidencia) }}" target="_blank" class="expediente-file-link">
                                Ver evidencia
                            </a>
                        @endif
                    </article>
                @empty
                    <div class="expediente-empty-state">No hay experiencia laboral registrada.</div>
                @endforelse
            </section>

            <section class="expediente-panel">
                <div class="expediente-panel-header">
                    <div>
                        <h4>Trayectoria educativa</h4>
                        <p>Atestados, formación académica, educación continua y evidencias.</p>
                    </div>

                    @if(\Illuminate\Support\Facades\Route::has('fac.consultores.formacion.edit'))
                        <a href="{{ route('fac.consultores.formacion.edit', $consultor) }}" class="btn btn-sm btn-outline-secondary">
                            Editar trayectoria
                        </a>
                    @endif
                </div>

                @forelse($atestados as $atestado)
                    <article class="expediente-timeline-card">
                        <h5>{{ $atestado->titulo ?? $atestado->descripcion ?? 'Atestado no registrado' }}</h5>
                        <p>{{ $atestado->institucion ?? 'Institución no registrada' }}</p>

                        <div class="expediente-tag-row">
                            <span>{{ $atestado->tipoFormacion?->nombre ?? 'Tipo de formación no registrado' }}</span>
                            <span>{{ $atestado->tipoAtestado?->nombre ?? 'Tipo de atestado no registrado' }}</span>
                            <span>{{ $atestado->nivelAcademico?->nombre ?? 'Nivel no registrado' }}</span>
                            <span>{{ $atestado->pais?->nombre_pais ?? 'País no registrado' }}</span>

                            @if($atestado->horas)
                                <span>{{ $atestado->horas }} horas</span>
                            @endif
                        </div>

                        <span class="expediente-date">
                            {{ $atestado->fecha_inicio ? $atestado->fecha_inicio->format('d/m/Y') : 'S/F' }}
                            -
                            {{ $atestado->fecha_fin ? $atestado->fecha_fin->format('d/m/Y') : 'S/F' }}
                        </span>

                        @if($atestado->descripcion)
                            <p class="expediente-description">{{ $atestado->descripcion }}</p>
                        @endif

                        @if($atestado->url_archivo)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($atestado->url_archivo) }}" target="_blank" class="expediente-file-link">
                                Ver archivo
                            </a>
                        @endif
                    </article>
                @empty
                    <div class="expediente-empty-state">No hay atestados registrados.</div>
                @endforelse
            </section>

            <section class="expediente-panel">
                <div class="expediente-panel-header">
                    <div>
                        <h4>Capacitaciones FEPADE</h4>
                        <p>Capacitaciones impartidas o registradas desde FEPADE.</p>
                    </div>
                </div>

                @forelse($capacitacionesFepade as $capacitacion)
                    <article class="expediente-timeline-card">
                        <h5>{{ $capacitacion->nombre_evento ?? 'Capacitación no registrada' }}</h5>

                        <p>
                            {{ $capacitacion->tema ?? 'Tema no registrado' }}
                            @if($capacitacion->institucion)
                                · {{ $capacitacion->institucion }}
                            @endif
                        </p>

                        <div class="expediente-tag-row">
                            <span>{{ $capacitacion->modalidad ?? 'Modalidad no registrada' }}</span>

                            @if($capacitacion->horas)
                                <span>{{ $capacitacion->horas }} horas</span>
                            @endif

                            @if($capacitacion->fuente)
                                <span>{{ $capacitacion->fuente }}</span>
                            @endif
                        </div>

                        <span class="expediente-date">
                            {{ $capacitacion->fecha_inicio ? $capacitacion->fecha_inicio->format('d/m/Y') : 'S/F' }}
                            -
                            {{ $capacitacion->fecha_fin ? $capacitacion->fecha_fin->format('d/m/Y') : 'S/F' }}
                        </span>
                    </article>
                @empty
                    <div class="expediente-empty-state">No hay capacitaciones FEPADE registradas.</div>
                @endforelse
            </section>

            <section class="expediente-panel">
                <div class="expediente-panel-header">
                    <div>
                        <h4>Referencias</h4>
                        <p>Contactos personales y laborales asociados al perfil.</p>
                    </div>

                    @if(\Illuminate\Support\Facades\Route::has('fac.consultores.referencias.edit'))
                        <a href="{{ route('fac.consultores.referencias.edit', $consultor) }}" class="btn btn-sm btn-outline-secondary">
                            Editar referencias
                        </a>
                    @endif
                </div>

                <div class="expediente-cards-grid">
                    @forelse($referencias as $referencia)
                        @php
                            $tipoReferencia = $tiposReferencia->get($referencia->id_tipo_referencia);
                            $tipoRelacion = $tiposRelacion->get($referencia->id_tipo_relacion);
                        @endphp

                        <div class="expediente-mini-card">
                            <h5>{{ $referencia->nombre ?? 'Referencia sin nombre' }}</h5>
                            <p>{{ $tipoReferencia->nombre ?? 'Tipo no registrado' }}</p>
                            <span>{{ $tipoRelacion->nombre ?? 'Relación no registrada' }}</span>
                            <span>{{ $referencia->telefono ?? 'Sin teléfono' }}</span>
                            <span>{{ $referencia->correo ?? 'Sin correo' }}</span>

                            @if($referencia->empresa || $referencia->cargo)
                                <span>
                                    {{ $referencia->cargo ?? 'Cargo no registrado' }}
                                    @if($referencia->empresa)
                                        · {{ $referencia->empresa }}
                                    @endif
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="expediente-empty-state">No hay referencias registradas.</div>
                    @endforelse
                </div>
            </section>

        </main>

        <aside class="expediente-sidebar">

            <section class="expediente-panel">
                <div class="expediente-panel-header compact">
                    <div>
                        <h4>Contacto</h4>
                    </div>

                    @if(\Illuminate\Support\Facades\Route::has('fac.consultores.contacto.edit'))
                        <a href="{{ route('fac.consultores.contacto.edit', $consultor) }}" class="btn btn-sm btn-outline-secondary">
                            Editar
                        </a>
                    @endif
                </div>

                @forelse($consultor->emails as $email)
                    <div class="expediente-side-item">
                        <strong>{{ $email->email }}</strong>
                        <span>{{ $email->principal ? 'Correo principal' : 'Correo adicional' }}</span>
                    </div>
                @empty
                    <div class="expediente-empty-state">Sin correos registrados.</div>
                @endforelse

                @forelse($consultor->telefonos as $telefono)
                    @php
                        $tipoTelefono = $tiposTelefono->get($telefono->id_tipo_telefono);
                    @endphp

                    <div class="expediente-side-item">
                        <strong>{{ $telefono->numero_telefono }}</strong>
                        <span>
                            {{ $tipoTelefono->nombre ?? 'Tipo no registrado' }}
                            @if($telefono->extension)
                                · Ext. {{ $telefono->extension }}
                            @endif
                        </span>
                    </div>
                @empty
                    <div class="expediente-empty-state mt-2">Sin teléfonos registrados.</div>
                @endforelse
            </section>

            <section class="expediente-panel">
                <div class="expediente-panel-header compact">
                    <h4>Redes sociales</h4>
                </div>

                @forelse($consultor->redesSociales as $red)
                    @php
                        $tipoRed = $tiposRedSocial->get($red->id_tipo_red_social);
                    @endphp

                    <div class="expediente-side-item">
                        <strong>{{ $tipoRed->nombre ?? 'Red social' }}</strong>
                        <a href="{{ $red->enlace }}" target="_blank" class="expediente-file-link">
                            Abrir enlace
                        </a>
                    </div>
                @empty
                    <div class="expediente-empty-state">Sin redes sociales registradas.</div>
                @endforelse
            </section>

            <section class="expediente-panel">
                <div class="expediente-panel-header compact">
                    <h4>Emergencia</h4>
                </div>

                @forelse($consultor->emergencias as $emergencia)
                    <div class="expediente-side-item">
                        <strong>{{ $emergencia->nombre ?? 'Contacto sin nombre' }}</strong>
                        <span>{{ $emergencia->telefono ?? 'Sin teléfono' }}</span>
                        <span>{{ $emergencia->correo ?? 'Sin correo' }}</span>
                    </div>
                @empty
                    <div class="expediente-empty-state">Sin contactos de emergencia.</div>
                @endforelse
            </section>

            <section class="expediente-panel">
                <div class="expediente-panel-header compact">
                    <h4>Disponibilidad</h4>
                </div>

                <div class="expediente-tag-row vertical">
                    @forelse($disponibilidades as $disponibilidad)
                        @php
                            $tipoDisponibilidad = $tiposDisponibilidad->get($disponibilidad->id_tipo_disponibilidad);
                        @endphp

                        <span>{{ $tipoDisponibilidad->nombre ?? 'Disponibilidad no registrada' }}</span>
                    @empty
                        <div class="expediente-empty-state">Sin disponibilidad registrada.</div>
                    @endforelse
                </div>
            </section>

            <section class="expediente-panel">
                <div class="expediente-panel-header compact">
                    <h4>Idiomas</h4>
                </div>

                @forelse($idiomas as $consultorIdioma)
                    @php
                        $idioma = $idiomasCatalogo->get($consultorIdioma->id_idioma);
                        $nivel = $nivelesIdioma->get($consultorIdioma->id_idioma_nivel);
                    @endphp

                    <div class="expediente-side-item">
                        <strong>{{ $idioma->nombre ?? 'Idioma no registrado' }}</strong>
                        <span>{{ $nivel->nombre ?? 'Nivel no registrado' }}</span>

                        @if($consultorIdioma->url_certificado)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($consultorIdioma->url_certificado) }}" target="_blank" class="expediente-file-link">
                                Ver certificado
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="expediente-empty-state">Sin idiomas registrados.</div>
                @endforelse
            </section>

            <section class="expediente-panel">
                <div class="expediente-panel-header compact">
                    <h4>Áreas de especialización</h4>
                </div>

                @forelse($areasPerfil as $registroArea)
                    @php
                        $area = $registroArea->areaEspecializacion ?? $areasCatalogo->get($registroArea->id_area_especializacion);
                        $atestado = $registroArea->atestado;
                        $capacitacion = $registroArea->capacitacionFepade;
                    @endphp

                    <div class="mb-3">
                        <h6 class="expediente-sidebar-title">
                            {{ $area->nombre ?? 'Área no registrada' }}
                        </h6>

                        <div class="small text-muted mb-2">
                            @if($atestado)
                                Atestado: {{ $atestado->titulo ?? $atestado->descripcion ?? 'Atestado registrado' }}
                            @elseif($capacitacion)
                                Capacitación FEPADE: {{ $capacitacion->nombre_evento ?? 'Evento registrado' }}
                            @else
                                Sin evidencia vinculada.
                            @endif
                        </div>

                        <div class="expediente-tag-row vertical">
                            @forelse($registroArea->habilidades as $detalleHabilidad)
                                @php
                                    $habilidadTecnica = $detalleHabilidad->habilidadTecnica ?? $habilidadesTecnicasCatalogo->get($detalleHabilidad->id_habilidad_tecnica);
                                @endphp
                                <span>{{ $habilidadTecnica->nombre ?? 'Habilidad técnica no registrada' }}</span>
                            @empty
                                <span class="text-muted">Sin habilidades técnicas registradas.</span>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="expediente-empty-state">Sin áreas de especialización registradas.</div>
                @endforelse
            </section>

        </aside>

    </div>
</div>

@endsection