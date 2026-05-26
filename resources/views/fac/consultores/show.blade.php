@extends('layouts.app')

@section('title', 'Expediente consultor | Facilitadores FEPADE')
@section('page-title', 'Expediente consultor')
@section('page-subtitle', 'Vista resumen del perfil del consultor')

@section('content')

<x-ui.page-header title="Expediente del consultor" subtitle="Información general del perfil registrado.">
    <div class="d-flex gap-2">
        <a href="{{ route('fac.consultores.edit', $consultor) }}" class="btn btn-fepade">
            Editar datos personales
        </a>

        <a href="{{ route('fac.consultores.index') }}" class="btn btn-outline-secondary">
            Volver
        </a>
    </div>
</x-ui.page-header>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="fepade-card h-100 expediente-profile-card">
            <div class="text-center">
                @if($consultor->ruta_foto)
                    <img 
                        src="{{ \Illuminate\Support\Facades\Storage::url($consultor->ruta_foto) }}" 
                        alt="Foto de {{ $consultor->nombre_completo }}"
                        class="expediente-avatar-img"
                    >
                @else
                    <div class="expediente-avatar">
                        {{ strtoupper(substr($consultor->nombres, 0, 1) . substr($consultor->apellidos, 0, 1)) }}
                    </div>
                @endif
                <h4 class="mb-1">{{ $consultor->nombre_completo }}</h4>
                <p class="text-muted mb-3">{{ $consultor->nacionalidad ?? 'Nacionalidad no registrada' }}</p>

                @if($consultor->activo)
                    <span class="badge badge-success-soft">Activo</span>
                @else
                    <span class="badge badge-warning-soft">Inactivo</span>
                @endif

                @if($consultor->vigente)
                    <span class="badge badge-info-soft ms-1">Vigente</span>
                @else
                    <span class="badge badge-warning-soft ms-1">No vigente</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="fepade-card h-100">
            <div class="expediente-section-header">
                <div>
                    <h5>Datos personales</h5>
                    <p>Información principal del consultor.</p>
                </div>

                <a href="{{ route('fac.consultores.edit', $consultor) }}" class="btn btn-sm btn-outline-secondary">
                    Editar
                </a>
            </div>

            <div class="expediente-grid">
                <div>
                    <span class="expediente-label">Tipo de identificación</span>
                    <span class="expediente-value">{{ $consultor->tipo_identificacion ?? 'No registrado' }}</span>
                </div>

                <div>
                    <span class="expediente-label">Número de identificación</span>
                    <span class="expediente-value">{{ $consultor->numero_identificacion ?? 'No registrado' }}</span>
                </div>

                <div>
                    <span class="expediente-label">NIT</span>
                    <span class="expediente-value">{{ $consultor->nit ?? 'No registrado' }}</span>
                </div>

                <div>
                    <span class="expediente-label">NRC</span>
                    <span class="expediente-value">{{ $consultor->nrc ?? 'No registrado' }}</span>
                </div>

                <div>
                    <span class="expediente-label">Sexo</span>
                    <span class="expediente-value">{{ $consultor->sexoCatalogo?->nombre ?? 'No registrado' }}</span>
                </div>

                <div>
                    <span class="expediente-label">Fecha de nacimiento</span>
                    <span class="expediente-value">
                        {{ $consultor->fecha_nacimiento ? $consultor->fecha_nacimiento->format('d/m/Y') : 'No registrada' }}
                    </span>
                </div>

                <div>
                    <span class="expediente-label">Estado civil</span>
                    <span class="expediente-value">{{ $consultor->estado_civil ?? 'No registrado' }}</span>
                </div>

                <div>
                    <span class="expediente-label">Nacionalidad</span>
                    <span class="expediente-value">{{ $consultor->nacionalidad ?? 'No registrada' }}</span>
                </div>

                <div class="expediente-grid-full">
                    <span class="expediente-label">Dirección de residencia</span>
                    <span class="expediente-value">{{ $consultor->direccion_residencia ?? 'No registrada' }}</span>
                </div>
            </div>

            <hr class="formacion-divider">

            @php
                $documentoIdentificacion = $consultor->documentos->first(function ($documento) use ($catalogos, $consultor) {
                    $tipo = $catalogos['tiposDocumento']->get($documento->id_tipo_documento);

                    return $tipo && strtolower($tipo->nombre) === strtolower($consultor->tipo_identificacion ?? '');
                });

                $documentoNit = $consultor->documentos->first(function ($documento) use ($catalogos) {
                    $tipo = $catalogos['tiposDocumento']->get($documento->id_tipo_documento);

                    return $tipo && strtolower($tipo->nombre) === 'nit';
                });

                $documentoNrc = $consultor->documentos->first(function ($documento) use ($catalogos) {
                    $tipo = $catalogos['tiposDocumento']->get($documento->id_tipo_documento);

                    return $tipo && strtolower($tipo->nombre) === 'nrc';
                });
            @endphp

            <h6 class="expediente-subtitle mb-3">
                Documentos de identificación
            </h6>

            <div class="row g-3">

                {{-- DUI / PASAPORTE --}}
                <div class="col-md-4">
                    <div class="expediente-item h-100">

                        <div class="fw-semibold mb-2">
                            {{ $consultor->tipo_identificacion ?? 'Documento de identificación' }}
                        </div>

                        <div class="mb-2">
                            <span class="expediente-label">
                                Número
                            </span>

                            <span class="expediente-value">
                                {{ $consultor->numero_identificacion ?? 'No registrado' }}
                            </span>
                        </div>

                        @if($documentoIdentificacion?->url_archivo)
                            <a
                                href="{{ Storage::url($documentoIdentificacion->url_archivo) }}"
                                target="_blank"
                                class="formacion-document-link"
                            >
                                Ver documento
                            </a>
                        @else
                            <div class="text-muted small">
                                Sin documento adjunto
                            </div>
                        @endif
                    </div>
                </div>

                {{-- NIT --}}
                <div class="col-md-4">
                    <div class="expediente-item h-100">

                        <div class="fw-semibold mb-2">
                            NIT
                        </div>

                        <div class="mb-2">
                            <span class="expediente-label">
                                Número
                            </span>

                            <span class="expediente-value">
                                {{ $consultor->nit ?? 'No registrado' }}
                            </span>
                        </div>

                        @if($documentoNit?->url_archivo)
                            <a
                                href="{{ Storage::url($documentoNit->url_archivo) }}"
                                target="_blank"
                                class="formacion-document-link"
                            >
                                Ver documento
                            </a>
                        @else
                            <div class="text-muted small">
                                Sin documento adjunto
                            </div>
                        @endif
                    </div>
                </div>

                {{-- NRC --}}
                <div class="col-md-4">
                    <div class="expediente-item h-100">

                        <div class="fw-semibold mb-2">
                            NRC
                        </div>

                        <div class="mb-2">
                            <span class="expediente-label">
                                Número
                            </span>

                            <span class="expediente-value">
                                {{ $consultor->nrc ?? 'No registrado' }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <span class="expediente-label">
                                Actividad / Giro
                            </span>

                            <span class="expediente-value">
                                {{ $documentoNrc?->actividad_giro ?? 'No registrado' }}
                            </span>
                        </div>

                        @if($documentoNrc?->url_archivo)
                            <a
                                href="{{ Storage::url($documentoNrc->url_archivo) }}"
                                target="_blank"
                                class="formacion-document-link"
                            >
                                Ver documento
                            </a>
                        @else
                            <div class="text-muted small">
                                Sin documento adjunto
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="fepade-card mt-4">
    <div class="expediente-section-header">
        <div>
            <h5>Información de contacto</h5>
            <p>Correos, teléfonos, redes sociales y contactos de emergencia.</p>
        </div>

        <a href="{{ route('fac.consultores.contacto.edit', $consultor) }}" class="btn btn-sm btn-outline-secondary">
            Editar contacto
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <h6 class="expediente-subtitle">Correos electrónicos</h6>

            @forelse($consultor->emails as $email)
                <div class="expediente-item">
                    <div class="fw-semibold">{{ $email->email }}</div>

                    @if($email->principal)
                        <span class="badge badge-success-soft mt-2">Principal</span>
                    @endif
                </div>
            @empty
                <div class="expediente-empty">No hay correos registrados.</div>
            @endforelse
        </div>

        <div class="col-lg-6">
            <h6 class="expediente-subtitle">Teléfonos</h6>

            @forelse($consultor->telefonos as $telefono)
                @php
                    $tipoTelefono = $catalogos['tiposTelefono']->get($telefono->id_tipo_telefono);
                @endphp

                <div class="expediente-item">
                    <div class="fw-semibold">{{ $telefono->numero_telefono }}</div>
                    <div class="text-muted small">
                        {{ $tipoTelefono->nombre ?? 'Tipo no registrado' }}

                        @if($telefono->extension)
                            · Ext. {{ $telefono->extension }}
                        @endif
                    </div>
                </div>
            @empty
                <div class="expediente-empty">No hay teléfonos registrados.</div>
            @endforelse
        </div>

        <div class="col-lg-6">
            <h6 class="expediente-subtitle">Redes sociales / enlaces</h6>

            @forelse($consultor->redesSociales as $red)
                @php
                    $tipoRed = $catalogos['tiposRedSocial']->get($red->id_tipo_red_social);
                @endphp

                <div class="expediente-item">
                    <div class="fw-semibold">{{ $tipoRed->nombre ?? 'Red social' }}</div>

                    <a href="{{ $red->enlace }}" target="_blank" rel="noopener" class="formacion-link">
                        {{ $red->enlace }}
                    </a>
                </div>
            @empty
                <div class="expediente-empty">No hay redes sociales registradas.</div>
            @endforelse
        </div>

        <div class="col-lg-6">
            <h6 class="expediente-subtitle">Contactos de emergencia</h6>

            @forelse($consultor->emergencias as $emergencia)
                <div class="expediente-item">
                    <div class="fw-semibold">{{ $emergencia->nombre }}</div>

                    <div class="text-muted small">
                        {{ $emergencia->telefono ?? 'Sin teléfono' }}

                        @if($emergencia->correo)
                            · {{ $emergencia->correo }}
                        @endif
                    </div>
                </div>
            @empty
                <div class="expediente-empty">No hay contactos de emergencia registrados.</div>
            @endforelse
        </div>
    </div>
</div>

<div class="fepade-card mt-4">
    <div class="expediente-section-header">
        <div>
            <h5>Formación académica y atestados</h5>
            <p>Educación formal, educación continua, certificados, títulos y constancias.</p>
        </div>

        <a href="{{ route('fac.consultores.formacion.edit', $consultor) }}" class="btn btn-sm btn-outline-secondary">
            Editar formación
        </a>
    </div>

    @php
        $formacionesAgrupadas = $consultor->formaciones->groupBy(function ($formacion) use ($catalogos) {
            $tipoAtestado = $catalogos['tiposAtestado']->get($formacion->id_tipo_atestado);
            return $tipoAtestado->id_tipo_formacion ?? 'sin_tipo';
        });
    @endphp

    @forelse($formacionesAgrupadas as $idTipoFormacion => $formaciones)
        @php
            $tipoFormacion = $catalogos['tiposFormacion']->get($idTipoFormacion);
        @endphp

        <div class="expediente-formacion-group">
            <h6 class="expediente-subtitle mb-3">
                {{ $tipoFormacion->nombre ?? 'Formación sin clasificar' }}
            </h6>

            @foreach($formaciones as $formacion)
                @php
                    $tipoAtestado = $catalogos['tiposAtestado']->get($formacion->id_tipo_atestado);
                    $nivel = $catalogos['nivelesAcademicos']->get($formacion->id_nivel_academico);
                    $pais = $catalogos['paises']->get($formacion->id_pais);
                @endphp

                <article class="formacion-card">
                    <div class="formacion-card-body">
                        <div class="formacion-card-header">
                            <div>
                                <h5 class="formacion-card-title">{{ $formacion->descripcion }}</h5>
                                <p class="formacion-card-type">{{ $tipoAtestado->nombre ?? 'Atestado' }}</p>
                            </div>
                        </div>

                        <div class="formacion-grid">
                            <div>
                                <span class="formacion-label">Institución</span>
                                <span class="formacion-value">{{ $formacion->institucion ?? 'No registrada' }}</span>
                            </div>

                            <div>
                                <span class="formacion-label">Nivel educativo</span>
                                <span class="formacion-value">{{ $nivel->nombre ?? 'No registrado' }}</span>
                            </div>

                            <div>
                                <span class="formacion-label">País</span>
                                <span class="formacion-value">{{ $pais->nombre_pais ?? 'No registrado' }}</span>
                            </div>

                            <div>
                                <span class="formacion-label">Periodo</span>
                                <span class="formacion-value">
                                    {{ $formacion->fecha_inicio ? $formacion->fecha_inicio->format('Y') : 'S/F' }}
                                    -
                                    {{ $formacion->fecha_fin ? $formacion->fecha_fin->format('Y') : 'S/F' }}
                                </span>
                            </div>
                        </div>

                        <hr class="formacion-divider">

                        @if($formacion->url)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($formacion->url) }}" target="_blank" class="formacion-document-link">
                                Ver documento
                            </a>
                        @else
                            <span class="formacion-document-empty">Sin documento adjunto</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @empty
        <div class="expediente-empty">
            No hay formación académica registrada.
        </div>
    @endforelse
</div>

<div class="fepade-card mt-4">
    <h5 class="mb-3">Secciones pendientes del expediente</h5>

    <div class="row g-3">
        <div class="col-md-4">
            <a href="{{ route('fac.consultores.experiencia.edit', $consultor) }}" class="expediente-module-card">
                <strong>Experiencia laboral</strong>
                <p>Experiencia, cargos, empresas y evidencia.</p>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('fac.consultores.experiencia.edit', $consultor) }}" class="expediente-module-card">
                <strong>Idiomas y habilidades</strong>
                <p>Competencias, idiomas y niveles.</p>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('fac.consultores.documentos.edit', $consultor) }}" class="expediente-module-card">
                <strong>Documentos</strong>
                <p>Documentos generales del expediente.</p>
            </a>
        </div>
    </div>
</div>

@endsection