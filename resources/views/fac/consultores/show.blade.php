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
    $habilidadesCatalogo = $catalogos['habilidades'] ?? collect();
    $tiposHabilidad = $catalogos['tiposHabilidad'] ?? collect();
    $idiomasCatalogo = $catalogos['idiomas'] ?? collect();
    $nivelesIdioma = $catalogos['nivelesIdioma'] ?? collect();
    $tiposReferencia = $catalogos['tiposReferencia'] ?? collect();
    $tiposRelacion = $catalogos['tiposRelacion'] ?? collect();
    $tiposConsultoria = $catalogos['tiposConsultoria'] ?? collect();

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
    $formaciones = $consultor->formaciones ?? collect();
    $disponibilidades = $consultor->disponibilidades ?? collect();
    $habilidades = $consultor->habilidades ?? collect();
    $idiomas = $consultor->idiomas ?? collect();
    $referencias = $consultor->referencias ?? collect();
    $consultorias = $consultor->tiposConsultoria ?? collect();
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
            <span>Formaciones</span>
            <strong>{{ $formaciones->count() }}</strong>
        </div>

        <div class="expediente-summary-card">
            <span>Idiomas</span>
            <strong>{{ $idiomas->count() }}</strong>
        </div>

        <div class="expediente-summary-card">
            <span>Habilidades</span>
            <strong>{{ $habilidades->count() }}</strong>
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
                        <h4>Formación académica</h4>
                        <p>Estudios, atestados, instituciones y evidencias.</p>
                    </div>

                    @if(\Illuminate\Support\Facades\Route::has('fac.consultores.formacion.edit'))
                        <a href="{{ route('fac.consultores.formacion.edit', $consultor) }}" class="btn btn-sm btn-outline-secondary">
                            Editar formación
                        </a>
                    @endif
                </div>

                @forelse($formaciones as $formacion)
                    @php
                        $tipoAtestado = $tiposAtestado->get($formacion->id_tipo_atestado);
                        $tipoFormacion = $tipoAtestado ? $tiposFormacion->get($tipoAtestado->id_tipo_formacion) : null;
                        $nivel = $nivelesAcademicos->get($formacion->id_nivel_academico);
                        $pais = $paises->get($formacion->id_pais);
                    @endphp

                    <article class="expediente-timeline-card">
                        <h5>{{ $formacion->descripcion ?? 'Formación no registrada' }}</h5>
                        <p>{{ $formacion->institucion ?? 'Institución no registrada' }}</p>

                        <div class="expediente-tag-row">
                            <span>{{ $tipoFormacion->nombre ?? 'Formación no registrada' }}</span>
                            <span>{{ $tipoAtestado->nombre ?? 'Atestado no registrado' }}</span>
                            <span>{{ $nivel->nombre ?? 'Nivel no registrado' }}</span>
                            <span>{{ $pais->nombre_pais ?? 'País no registrado' }}</span>
                        </div>

                        <span class="expediente-date">
                            {{ $formacion->fecha_inicio ? $formacion->fecha_inicio->format('d/m/Y') : 'S/F' }}
                            -
                            {{ $formacion->fecha_fin ? $formacion->fecha_fin->format('d/m/Y') : 'S/F' }}
                        </span>

                        @if($formacion->url)
                            <br>
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($formacion->url) }}" target="_blank" class="expediente-file-link">
                                Ver archivo
                            </a>
                        @endif
                    </article>
                @empty
                    <div class="expediente-empty-state">No hay formación académica registrada.</div>
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
                    <h4>Habilidades</h4>
                </div>

                @php
                    $habilidadesAgrupadas = $habilidades->groupBy(function ($consultorHabilidad) use ($habilidadesCatalogo) {
                        $habilidad = $habilidadesCatalogo->get($consultorHabilidad->id_habilidad);
                        return $habilidad->id_tipo_habilidad ?? 'sin_tipo';
                    });
                @endphp

                @forelse($habilidadesAgrupadas as $idTipoHabilidad => $habilidadesGrupo)
                    @php
                        $tipoHabilidad = $tiposHabilidad->get($idTipoHabilidad);
                    @endphp

                    <div class="mb-3">
                        <h6 class="expediente-sidebar-title">
                            {{ $tipoHabilidad->nombre ?? 'Habilidades sin clasificar' }}
                        </h6>

                        <div class="expediente-tag-row vertical">
                            @foreach($habilidadesGrupo as $consultorHabilidad)
                                @php
                                    $habilidad = $habilidadesCatalogo->get($consultorHabilidad->id_habilidad);
                                @endphp

                                <span>{{ $habilidad->nombre ?? 'Habilidad no registrada' }}</span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="expediente-empty-state">Sin habilidades registradas.</div>
                @endforelse
            </section>

            <section class="expediente-panel">
                <div class="expediente-panel-header compact">
                    <h4>Tipos de consultoría</h4>
                </div>

                <div class="expediente-tag-row vertical">
                    @forelse($consultorias as $consultorTipoConsultoria)
                        @php
                            $tipoConsultoria = $tiposConsultoria->get($consultorTipoConsultoria->id_tipo_consultoria);
                        @endphp

                        <span>{{ $tipoConsultoria->nombre ?? 'Tipo de consultoría no registrado' }}</span>
                    @empty
                        <div class="expediente-empty-state">Sin tipos de consultoría registrados.</div>
                    @endforelse
                </div>
            </section>

        </aside>

    </div>
</div>

@endsection