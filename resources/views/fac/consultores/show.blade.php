@extends('layouts.app')

@section('title', 'Expediente consultor | Facilitadores FEPADE')
@section('page-title', 'Expediente consultor')
@section('page-subtitle', 'Vista integral del perfil del consultor')

@section('content')

@php
    use App\Modules\Fac\Support\ProfileProgressPresenter;
    use App\Modules\Fac\Support\ProfessionalSummaryPresenter;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Storage;

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

    $documentoIdentificacion =
        $consultor->documentos
            ->first(function ($documento) use ($tiposDocumento) {
                $tipo =
                    $tiposDocumento->get(
                        $documento->id_tipo_documento
                    );

                if (! $tipo) {
                    return false;
                }

                return ! in_array(
                    strtoupper(
                        trim($tipo->nombre)
                    ),
                    [
                        'NIT',
                        'NRC',
                    ],
                    true
                );
            });

    $tipoIdentificacionMostrado =
        $documentoIdentificacion
            ? $tiposDocumento
                ->get(
                    $documentoIdentificacion
                        ->id_tipo_documento
                )
                ?->nombre
            : $consultor->tipo_identificacion;

    $numeroIdentificacionMostrado =
        $documentoIdentificacion?->numero
        ?? $consultor->numero_identificacion;

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
    $puntosFijos = collect($avancePerfil['puntos_fijos'] ?? []);
    $puntosDinamicos = collect($avancePerfil['puntos_dinamicos'] ?? []);
    $todosLosCriterios = $puntosFijos->merge($puntosDinamicos);
    $porcentajePerfil = $avancePerfil['porcentaje'] ?? 0;
    $criteriosPresentados = ProfileProgressPresenter::presentCollection($todosLosCriterios);
    $pendientesPresentados = ProfileProgressPresenter::pendingMessages($todosLosCriterios);

    $routeIfExists = function (string $name, $parameter = null) {
        if (! Route::has($name)) {
            return null;
        }

        return $parameter ? route($name, $parameter) : route($name);
    };

    $rutasEdicion = [
        'perfil' => $routeIfExists('fac.consultores.edit', $consultor),
        'contacto' => $routeIfExists('fac.consultores.contacto.edit', $consultor),
        'experiencia' => $routeIfExists('fac.consultores.experiencia.edit', $consultor),
        'formacion' => $routeIfExists('fac.consultores.formacion.edit', $consultor),
        'habilidades' => $routeIfExists('fac.consultores.habilidades.edit', $consultor),
        'idiomas' => $routeIfExists('fac.consultores.idiomas.edit', $consultor),
        'referencias' => $routeIfExists('fac.consultores.referencias.edit', $consultor),
        'disponibilidad' => $routeIfExists('fac.consultores.disponibilidad.edit', $consultor),
        'documentos' =>
            $routeIfExists(
                'fac.consultores.edit',
                $consultor
            )
                ? route(
                    'fac.consultores.edit',
                    $consultor
                ) . '#documentos-identificacion'
                : null,
        'index' => $routeIfExists('fac.consultores.index'),
    ];

    $habilidadesTotal = $areasPerfil->sum(function ($registroArea) {
        return $registroArea->habilidades?->count() ?? 0;
    });

    $documentosTotal = collect([$documentoIdentificacion, $documentoNit, $documentoNrc])->filter()->count() + $documentosGenerales->count();

    $areasNombres = $areasPerfil->map(function ($registroArea) use ($areasCatalogo) {
        $area = $registroArea->areaEspecializacion ?? $areasCatalogo->get($registroArea->id_area_especializacion);
        return $area->nombre ?? null;
    })->filter()->unique()->values();

    $primerasAreas = $areasNombres->take(3)->implode(', ');

    $estadoPerfil = match (true) {
        $porcentajePerfil >= 90 => ['texto' => 'Perfil listo para evaluación', 'clase' => 'success'],
        $porcentajePerfil >= 70 => ['texto' => 'Perfil avanzado', 'clase' => 'warning'],
        default => ['texto' => 'Perfil en construcción', 'clase' => 'danger'],
    };

    $resumenProfesional = ProfessionalSummaryPresenter::make($consultor);

    $ultimaActualizacion = $consultor->updated_at ? $consultor->updated_at->format('d/m/Y') : 'No registrada';
@endphp

<x-ui.page-header title="Expediente del consultor" subtitle="Vista integral del perfil profesional registrado.">
    <div class="d-flex gap-2 flex-wrap">
        @if($rutasEdicion['perfil'])
            <a href="{{ $rutasEdicion['perfil'] }}" class="btn btn-fepade">
                Editar perfil
            </a>
        @endif

        @if($rutasEdicion['index'])
            <a href="{{ $rutasEdicion['index'] }}" class="btn btn-outline-secondary">
                Volver
            </a>
        @endif
    </div>
</x-ui.page-header>

<div class="expediente-page expediente-ux">

    <section class="expediente-ux-hero">
        <div class="expediente-ux-hero-content">
            <div class="expediente-ux-avatar">
                @if($consultor->ruta_foto)
                    <img src="{{ Storage::url($consultor->ruta_foto) }}" alt="Foto de {{ $nombreCompleto }}">
                @else
                    <span>{{ $iniciales }}</span>
                @endif
            </div>

            <div class="expediente-ux-identity">
                <div class="expediente-ux-kicker">Expediente profesional</div>
                <h2>{{ $nombreCompleto ?: 'Consultor sin nombre' }}</h2>

                <div class="expediente-ux-meta">
                    <span>{{ $consultor->sexoCatalogo?->nombre ?? 'Sexo no registrado' }}</span>
                    <span>{{ $consultor->nacionalidad ?? 'Nacionalidad no registrada' }}</span>
                    <span>{{ $consultor->fecha_nacimiento ? $consultor->fecha_nacimiento->format('d/m/Y') : 'Fecha de nacimiento no registrada' }}</span>
                </div>

                <div class="expediente-ux-badges">
                    <span class="expediente-ux-badge {{ $consultor->activo ? 'success' : 'danger' }}">
                        {{ $consultor->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                    <span class="expediente-ux-badge {{ $consultor->vigente ? 'success' : 'warning' }}">
                        {{ $consultor->vigente ? 'Vigente' : 'No vigente' }}
                    </span>
                    <span class="expediente-ux-badge {{ $estadoPerfil['clase'] }}">
                        {{ $estadoPerfil['texto'] }}
                    </span>
                </div>
            </div>
        </div>

        <div class="expediente-ux-hero-side">
            <div class="expediente-ux-progress-ring" style="--progress: {{ $porcentajePerfil }}">
                <strong>{{ $porcentajePerfil }}%</strong>
                <span>completo</span>
            </div>

            <div class="expediente-ux-hero-actions">
                @if($rutasEdicion['perfil'])
                    <a href="{{ $rutasEdicion['perfil'] }}" class="btn btn-light">
                        Editar perfil
                    </a>
                @endif

                @if($rutasEdicion['index'])
                    <a href="{{ $rutasEdicion['index'] }}" class="btn btn-outline-light">
                        Volver
                    </a>
                @endif
            </div>
        </div>
    </section>

    <section class="expediente-ux-snapshot">
        <div class="expediente-ux-stat primary">
            <span>Avance oficial</span>
            <strong>{{ $porcentajePerfil }}%</strong>
            <small>{{ $avancePerfil['obtenidos'] ?? 0 }} de {{ $avancePerfil['total'] ?? 0 }} criterios</small>
        </div>

        <div class="expediente-ux-stat">
            <span>Experiencia</span>
            <strong>{{ $experiencias->count() }}</strong>
            <small>registro(s) laborales</small>
        </div>

        <div class="expediente-ux-stat">
            <span>Formación</span>
            <strong>{{ $atestados->count() + $capacitacionesFepade->count() }}</strong>
            <small>atestados y capacitaciones</small>
        </div>

        <div class="expediente-ux-stat">
            <span>Especialización</span>
            <strong>{{ $areasPerfil->count() }}</strong>
            <small>{{ $habilidadesTotal }} habilidad(es)</small>
        </div>

        <div class="expediente-ux-stat">
            <span>Idiomas</span>
            <strong>{{ $idiomas->count() }}</strong>
            <small>registrado(s)</small>
        </div>
    </section>

    <x-ui.wizard-progress
        title="Completitud del expediente"
        description="Estos criterios definen el avance oficial del perfil profesional del consultor."
        :steps="[]"
        :current="null"
        :completion="$porcentajePerfil"
        :criteria-fixed="$puntosFijos"
        :criteria-dynamic="$puntosDinamicos"
        :presented-criteria="$criteriosPresentados"
        :pending-messages="$pendientesPresentados"
        :obtained="$avancePerfil['obtenidos'] ?? null"
        :total="$avancePerfil['total'] ?? null"
    />

    <section class="expediente-ux-summary-panel">
        <div>
            <span class="expediente-ux-kicker dark">Resumen profesional</span>
            <h3>Lectura rápida del perfil</h3>
            <p>{{ $resumenProfesional }}</p>
        </div>

        <div class="expediente-ux-summary-contact">
            <div>
                <span>Correo principal</span>
                <strong>{{ $correoPrincipal->email ?? 'No registrado' }}</strong>
            </div>
            <div>
                <span>Teléfono</span>
                <strong>{{ $telefonoPrincipal->numero_telefono ?? 'No registrado' }}</strong>
            </div>
            <div>
                <span>Actualizado</span>
                <strong>{{ $ultimaActualizacion }}</strong>
            </div>
        </div>
    </section>

    <div class="expediente-layout">
        <main class="expediente-main">

            <section class="expediente-panel expediente-ux-panel">
                <div class="expediente-panel-header">
                    <div>
                        <span class="expediente-ux-section-icon"><i class="fa-solid fa-user"></i></span>
                        <h4>Datos personales</h4>
                        <p>Información general, identificación y residencia.</p>
                    </div>

                    @if($rutasEdicion['perfil'])
                        <a href="{{ $rutasEdicion['perfil'] }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                    @endif
                </div>

                <div class="expediente-info-grid">
                    <div class="expediente-info-item"><span>Nombres</span><strong>{{ $consultor->nombres ?? 'No registrado' }}</strong></div>
                    <div class="expediente-info-item"><span>Apellidos</span><strong>{{ $consultor->apellidos ?? 'No registrado' }}</strong></div>
                    <div class="expediente-info-item"><span>Apellido de casa</span><strong>{{ $consultor->apellido_casa ?? 'No registrado' }}</strong></div>
                    <div class="expediente-info-item"><span>Estado civil</span><strong>{{ $consultor->estado_civil ?? 'No registrado' }}</strong></div>
                    <div class="expediente-info-item"><span>Nacionalidad</span><strong>{{ $consultor->nacionalidad ?? 'No registrada' }}</strong></div>
                    <div class="expediente-info-item"><span>Sexo</span><strong>{{ $consultor->sexoCatalogo?->nombre ?? 'No registrado' }}</strong></div>
                    <div class="expediente-info-item"><span>Tipo de identificación</span><strong>{{ $tipoIdentificacionMostrado ?? 'No registrado' }}</strong></div>
                    <div class="expediente-info-item"><span>Número de identificación</span><strong>{{ $numeroIdentificacionMostrado ?? 'No registrado' }}</strong></div>
                    <div class="expediente-info-item"><span>NIT</span><strong>{{ $consultor->nit ?? 'No registrado' }}</strong></div>
                    <div class="expediente-info-item"><span>NRC</span><strong>{{ $consultor->nrc ?? 'No registrado' }}</strong></div>
                    <div class="expediente-info-item"><span>Fecha de nacimiento</span><strong>{{ $consultor->fecha_nacimiento ? $consultor->fecha_nacimiento->format('d/m/Y') : 'No registrada' }}</strong></div>
                    <div class="expediente-info-item"><span>Vigencia</span><strong>{{ $consultor->vigente ? 'Vigente' : 'No vigente' }}</strong></div>
                    <div class="expediente-info-item wide"><span>Dirección de residencia</span><strong>{{ $consultor->direccion_residencia ?? 'No registrada' }}</strong></div>
                </div>
            </section>

            <section class="expediente-panel expediente-ux-panel">
                <div class="expediente-panel-header">
                    <div>
                        <span class="expediente-ux-section-icon"><i class="fa-solid fa-id-card"></i></span>
                        <h4>Documentos de identificación</h4>
                        <p>Archivos principales asociados a la identificación fiscal y personal.</p>
                    </div>

                    @if($rutasEdicion['documentos'])
                        <a href="{{ $rutasEdicion['documentos'] }}" class="btn btn-sm btn-outline-secondary">Editar documentos</a>
                    @endif
                </div>

                <div class="expediente-cards-grid">
                    <div class="expediente-mini-card expediente-ux-doc-card">
                        <h5>{{ $tipoIdentificacionMostrado ?? 'Documento de identificación' }}</h5>
                        <p>Número:{{ $numeroIdentificacionMostrado ?? 'No registrado' }}</p>
                        @if($documentoIdentificacion?->url_archivo)
                            <a href="{{ Storage::url($documentoIdentificacion->url_archivo) }}" target="_blank" class="expediente-file-link">Ver documento</a>
                        @else
                            <span>Sin documento adjunto</span>
                        @endif
                    </div>

                    <div class="expediente-mini-card expediente-ux-doc-card">
                        <h5>NIT</h5>
                        <p>Número: {{ $consultor->nit ?? 'No registrado' }}</p>
                        @if($documentoNit?->url_archivo)
                            <a href="{{ Storage::url($documentoNit->url_archivo) }}" target="_blank" class="expediente-file-link">Ver documento</a>
                        @else
                            <span>Sin documento adjunto</span>
                        @endif
                    </div>

                    <div class="expediente-mini-card expediente-ux-doc-card">
                        <h5>NRC</h5>
                        <p>Número: {{ $consultor->nrc ?? 'No registrado' }}</p>
                        <p>Actividad / giro: {{ $documentoNrc?->actividad_giro ?? 'No registrado' }}</p>
                        @if($documentoNrc?->url_archivo)
                            <a href="{{ Storage::url($documentoNrc->url_archivo) }}" target="_blank" class="expediente-file-link">Ver documento</a>
                        @else
                            <span>Sin documento adjunto</span>
                        @endif
                    </div>
                </div>
            </section>

            <section class="expediente-panel expediente-ux-panel">
                <div class="expediente-panel-header">
                    <div>
                        <span class="expediente-ux-section-icon"><i class="fa-solid fa-briefcase"></i></span>
                        <h4>Experiencia laboral</h4>
                        <p>Trayectoria profesional, cargos, empresas y evidencias.</p>
                    </div>

                    @if($rutasEdicion['experiencia'])
                        <a href="{{ $rutasEdicion['experiencia'] }}" class="btn btn-sm btn-outline-secondary">Editar experiencia</a>
                    @endif
                </div>

                <div class="expediente-ux-timeline">
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
                                <div class="mt-2"><span class="badge badge-success-soft">Trabajo actual</span></div>
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
                                <a href="{{ Storage::url($experiencia->url_evidencia) }}" target="_blank" class="expediente-file-link">Ver evidencia</a>
                            @endif
                        </article>
                    @empty
                        <div class="expediente-empty-state">No hay experiencia laboral registrada.</div>
                    @endforelse
                </div>
            </section>

            <section class="expediente-panel expediente-ux-panel">
                <div class="expediente-panel-header">
                    <div>
                        <span class="expediente-ux-section-icon"><i class="fa-solid fa-graduation-cap"></i></span>
                        <h4>Trayectoria educativa</h4>
                        <p>Atestados, formación académica, educación continua y evidencias.</p>
                    </div>

                    @if($rutasEdicion['formacion'])
                        <a href="{{ $rutasEdicion['formacion'] }}" class="btn btn-sm btn-outline-secondary">Editar trayectoria</a>
                    @endif
                </div>

                <div class="expediente-ux-timeline">
                    @forelse($atestados as $atestado)
                        <article class="expediente-timeline-card">
                            <h5>{{ $atestado->titulo ?? $atestado->descripcion ?? 'Atestado no registrado' }}</h5>
                            <p>{{ $atestado->institucion ?? 'Institución no registrada' }}</p>

                            <div class="expediente-tag-row">
                                <span>{{ $atestado->tipoFormacion?->nombre ?? 'Tipo de formación no registrado' }}</span>
                                <span>{{ $atestado->tipoAtestado?->nombre ?? 'Tipo de atestado no registrado' }}</span>
                                <span>{{ $atestado->nivelAcademico?->nombre ?? 'Nivel no registrado' }}</span>
                                <span>{{ $atestado->pais?->nombre_pais ?? 'País no registrado' }}</span>
                                @if($atestado->horas)<span>{{ $atestado->horas }} horas</span>@endif
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
                                <a href="{{ Storage::url($atestado->url_archivo) }}" target="_blank" class="expediente-file-link">Ver archivo</a>
                            @endif
                        </article>
                    @empty
                        <div class="expediente-empty-state">No hay atestados registrados.</div>
                    @endforelse
                </div>
            </section>

            <section class="expediente-panel expediente-ux-panel">
                <div class="expediente-panel-header">
                    <div>
                        <span class="expediente-ux-section-icon"><i class="fa-solid fa-chalkboard-user"></i></span>
                        <h4>Capacitaciones FEPADE</h4>
                        <p>Capacitaciones impartidas o registradas desde FEPADE.</p>
                    </div>

                    @if($rutasEdicion['formacion'])
                        <a href="{{ $rutasEdicion['formacion'] }}" class="btn btn-sm btn-outline-secondary">Editar capacitaciones</a>
                    @endif
                </div>

                <div class="expediente-ux-timeline">
                    @forelse($capacitacionesFepade as $capacitacion)
                        <article class="expediente-timeline-card">

                            <h5>
                                {{ $capacitacion->curso_nombre
                                    ?? 'Capacitación no registrada' }}
                            </h5>

                            <p>
                                @if($capacitacion->cliente)
                                    {{ $capacitacion->cliente }}
                                @else
                                    Cliente no registrado
                                @endif
                            </p>

                            <div class="expediente-tag-row">

                                @if($capacitacion->modalidad)
                                    <span>
                                        {{ $capacitacion->modalidad }}
                                    </span>
                                @endif

                                @if($capacitacion->tipo_evento_nombre)
                                    <span>
                                        {{ $capacitacion->tipo_evento_nombre }}
                                    </span>
                                @endif

                                @if($capacitacion->estado_curso_nombre)
                                    <span>
                                        {{ $capacitacion->estado_curso_nombre }}
                                    </span>
                                @endif

                                @if($capacitacion->no_horas_real !== null)
                                    <span>
                                        {{ $capacitacion->no_horas_real }}
                                        horas
                                    </span>
                                @endif

                                @if($capacitacion->fuente)
                                    <span>
                                        {{ $capacitacion->fuente }}
                                    </span>
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
                </div>
            </section>

            <section class="expediente-panel expediente-ux-panel">
                <div class="expediente-panel-header">
                    <div>
                        <span class="expediente-ux-section-icon"><i class="fa-solid fa-address-book"></i></span>
                        <h4>Referencias</h4>
                        <p>Contactos personales y laborales asociados al perfil.</p>
                    </div>

                    @if($rutasEdicion['referencias'])
                        <a href="{{ $rutasEdicion['referencias'] }}" class="btn btn-sm btn-outline-secondary">Editar referencias</a>
                    @endif
                </div>

                <div class="expediente-cards-grid">
                    @forelse($referencias as $referencia)
                        @php
                            $tipoReferencia = $tiposReferencia->get($referencia->id_tipo_referencia);
                            $tipoRelacion = $tiposRelacion->get($referencia->id_tipo_relacion);
                        @endphp

                        <div class="expediente-mini-card expediente-ux-reference-card">
                            <h5>{{ $referencia->nombre ?? 'Referencia sin nombre' }}</h5>
                            <p>{{ $tipoReferencia->nombre ?? 'Tipo no registrado' }}</p>
                            <span>{{ $tipoRelacion->nombre ?? 'Relación no registrada' }}</span>
                            <span>{{ $referencia->telefono ?? 'Sin teléfono' }}</span>
                            <span>{{ $referencia->correo ?? 'Sin correo' }}</span>

                            @if($referencia->empresa || $referencia->cargo)
                                <span>
                                    {{ $referencia->cargo ?? 'Cargo no registrado' }}
                                    @if($referencia->empresa) · {{ $referencia->empresa }} @endif
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="expediente-empty-state">No hay referencias registradas.</div>
                    @endforelse
                </div>
            </section>
        </main>

        <aside class="expediente-sidebar expediente-ux-sidebar">
            <section class="expediente-panel expediente-ux-side-summary">
                <div class="expediente-sidebar-title">Resumen del expediente</div>

                <div class="expediente-ux-side-metric"><span>Documentos</span><strong>{{ $documentosTotal }}</strong></div>
                <div class="expediente-ux-side-metric"><span>Experiencia</span><strong>{{ $experiencias->count() }}</strong></div>
                <div class="expediente-ux-side-metric"><span>Formación</span><strong>{{ $atestados->count() + $capacitacionesFepade->count() }}</strong></div>
                <div class="expediente-ux-side-metric"><span>Idiomas</span><strong>{{ $idiomas->count() }}</strong></div>
                <div class="expediente-ux-side-metric"><span>Referencias</span><strong>{{ $referencias->count() }}</strong></div>
            </section>

            <section class="expediente-panel">
                <div class="expediente-panel-header compact">
                    <h4>Contacto</h4>
                    @if($rutasEdicion['contacto'])
                        <a href="{{ $rutasEdicion['contacto'] }}" class="btn btn-sm btn-outline-secondary">Editar</a>
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
                    @php $tipoTelefono = $tiposTelefono->get($telefono->id_tipo_telefono); @endphp
                    <div class="expediente-side-item">
                        <strong>{{ $telefono->numero_telefono }}</strong>
                        <span>{{ $tipoTelefono->nombre ?? 'Tipo no registrado' }} @if($telefono->extension) · Ext. {{ $telefono->extension }} @endif</span>
                    </div>
                @empty
                    <div class="expediente-empty-state mt-2">Sin teléfonos registrados.</div>
                @endforelse
            </section>

            <section class="expediente-panel">
                <div class="expediente-panel-header compact">
                    <h4>Redes sociales</h4>
                    @if($rutasEdicion['contacto'])
                        <a href="{{ $rutasEdicion['contacto'] }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                    @endif
                </div>

                @forelse($consultor->redesSociales as $red)
                    @php $tipoRed = $tiposRedSocial->get($red->id_tipo_red_social); @endphp
                    <div class="expediente-side-item">
                        <strong>{{ $tipoRed->nombre ?? 'Red social' }}</strong>
                        <a href="{{ $red->enlace }}" target="_blank" class="expediente-file-link">Abrir enlace</a>
                    </div>
                @empty
                    <div class="expediente-empty-state">Sin redes sociales registradas.</div>
                @endforelse
            </section>

            <section class="expediente-panel">
                <div class="expediente-panel-header compact">
                    <h4>Emergencia</h4>
                    @if($rutasEdicion['contacto'])
                        <a href="{{ $rutasEdicion['contacto'] }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                    @endif
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
                    @if($rutasEdicion['disponibilidad'])
                        <a href="{{ $rutasEdicion['disponibilidad'] }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                    @endif
                </div>

                <div class="expediente-tag-row vertical">
                    @forelse($disponibilidades as $disponibilidad)
                        @php $tipoDisponibilidad = $tiposDisponibilidad->get($disponibilidad->id_tipo_disponibilidad); @endphp
                        <span>{{ $tipoDisponibilidad->nombre ?? 'Disponibilidad no registrada' }}</span>
                    @empty
                        <div class="expediente-empty-state">Sin disponibilidad registrada.</div>
                    @endforelse
                </div>
            </section>

            <section class="expediente-panel">
                <div class="expediente-panel-header compact">
                    <h4>Idiomas</h4>
                    @if($rutasEdicion['idiomas'])
                        <a href="{{ $rutasEdicion['idiomas'] }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                    @endif
                </div>

                @forelse($idiomas as $consultorIdioma)
                    @php
                        $idioma = $idiomasCatalogo->get($consultorIdioma->id_idioma);
                        $nivel = $nivelesIdioma->get($consultorIdioma->id_idioma_nivel);
                    @endphp
                    <div class="expediente-side-item expediente-ux-language">
                        <strong>{{ $idioma->nombre ?? 'Idioma no registrado' }}</strong>
                        <span>{{ $nivel->nombre ?? 'Nivel no registrado' }}</span>
                        @if($consultorIdioma->url_certificado)
                            <a href="{{ Storage::url($consultorIdioma->url_certificado) }}" target="_blank" class="expediente-file-link">Ver certificado</a>
                        @endif
                    </div>
                @empty
                    <div class="expediente-empty-state">Sin idiomas registrados.</div>
                @endforelse
            </section>

            <section class="expediente-panel">
                <div class="expediente-panel-header compact">
                    <h4>Áreas de especialización</h4>
                    @if($rutasEdicion['habilidades'])
                        <a href="{{ $rutasEdicion['habilidades'] }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                    @endif
                </div>

                @forelse($areasPerfil as $registroArea)
                    @php
                        $area = $registroArea->areaEspecializacion ?? $areasCatalogo->get($registroArea->id_area_especializacion);
                        $atestado = $registroArea->atestado;
                        $capacitacion = $registroArea->capacitacionFepade;
                    @endphp

                    <div class="mb-3 expediente-ux-area-block">
                        <h6 class="expediente-sidebar-title">{{ $area->nombre ?? 'Área no registrada' }}</h6>

                        <div class="small text-muted mb-2">
                            @if($atestado)
                                Atestado: {{ $atestado->titulo ?? $atestado->descripcion ?? 'Atestado registrado' }}
                            @elseif($capacitacion)
                                Capacitación FEPADE: {{ $capacitacion->curso_nombre ?? 'Evento registrado' }}
                            @else
                                Sin evidencia vinculada.
                            @endif
                        </div>

                        <div class="expediente-tag-row vertical">
                            @forelse($registroArea->habilidades as $detalleHabilidad)
                                @php $habilidadTecnica = $detalleHabilidad->habilidadTecnica ?? $habilidadesTecnicasCatalogo->get($detalleHabilidad->id_habilidad_tecnica); @endphp
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
