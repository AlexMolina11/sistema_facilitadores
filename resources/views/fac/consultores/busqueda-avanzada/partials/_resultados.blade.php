@php
    $totalResultados = $totalConsultores ?? ($consultores->total() ?? 0);
@endphp

<section class="busqueda-resultados" data-total-resultados="{{ $totalResultados }}">
    <div class="busqueda-resultados-header fepade-card mb-3">
        <div>
            <span class="busqueda-section-kicker">Resultados</span>
            <h5>Consultores encontrados</h5>
            <p>
                {{ $totalResultados }}
                resultado{{ $totalResultados === 1 ? '' : 's' }}
                según los filtros aplicados.
            </p>
        </div>

        <div class="busqueda-resultados-total">
            <strong>{{ $totalResultados }}</strong>
            <span>perfil{{ $totalResultados === 1 ? '' : 'es' }}</span>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
        @forelse($consultores as $consultor)
            @php
                /*
                |--------------------------------------------------------------------------
                | Identidad
                |--------------------------------------------------------------------------
                */
                $nombreCompleto = $consultor->nombre_completo
                    ?: trim(
                        ($consultor->nombres ?? '') . ' ' .
                        ($consultor->apellidos ?? '')
                    );

                $iniciales = strtoupper(
                    mb_substr($consultor->nombres ?? 'C', 0, 1) .
                    mb_substr($consultor->apellidos ?? 'F', 0, 1)
                );

                /*
                |--------------------------------------------------------------------------
                | Contacto
                |--------------------------------------------------------------------------
                */
                $telefonoPrincipal = optional(
                    $consultor->telefonos->first()
                )->numero_telefono;

                $emailPrincipal =
                    optional(
                        $consultor->emails->firstWhere('principal', true)
                    )->email
                    ?? optional($consultor->emails->first())->email;

                /*
                |--------------------------------------------------------------------------
                | Completitud
                |--------------------------------------------------------------------------
                */
                $avancePerfil = $consultor->avancePerfil(
                    $consultor->tipos_referencia_avance
                );

                $porcentajePerfil = $avancePerfil['porcentaje'] ?? 0;

                $claseAvance = match (true) {
                    $porcentajePerfil >= 85 => 'success',
                    $porcentajePerfil >= 60 => 'warning',
                    default => 'danger',
                };

                $textoAvance = match (true) {
                    $porcentajePerfil >= 85 => 'Perfil avanzado',
                    $porcentajePerfil >= 60 => 'En progreso',
                    default => 'Incompleto',
                };

                /*
                |--------------------------------------------------------------------------
                | Áreas de especialización
                |--------------------------------------------------------------------------
                */
                $todasLasAreas = $consultor->areasEspecializacion
                    ->pluck('areaEspecializacion.nombre')
                    ->filter()
                    ->unique()
                    ->values();

                $areas = $todasLasAreas->take(3);

                $areasRestantes = max(
                    0,
                    $todasLasAreas->count() - $areas->count()
                );

                /*
                |--------------------------------------------------------------------------
                | Habilidades técnicas
                |--------------------------------------------------------------------------
                */
                $todasLasHabilidades = $consultor->areasEspecializacion
                    ->flatMap(fn ($area) => $area->habilidades)
                    ->pluck('habilidadTecnica.nombre')
                    ->filter()
                    ->unique()
                    ->values();

                $habilidadesTecnicas = $todasLasHabilidades->take(4);

                $habilidadesRestantes = max(
                    0,
                    $todasLasHabilidades->count() - $habilidadesTecnicas->count()
                );

                /*
                |--------------------------------------------------------------------------
                | Indicadores
                |--------------------------------------------------------------------------
                */
                $experienciasCount =
                    $consultor->experienciasLaborales->count();

                $formacionesCount =
                    $consultor->atestados->count()
                    + $consultor->capacitacionesFepade->count();

                $idiomasCount =
                    $consultor->idiomas->count();


                /*
                |--------------------------------------------------------------------------
                | Ubicación
                |--------------------------------------------------------------------------
                */
                $ubicacionTexto = $consultor->municipio?->nombre_distrito;

                /*
                |--------------------------------------------------------------------------
                | Disponibilidad
                |--------------------------------------------------------------------------
                */
                $tieneDisponibilidad =
                    $consultor->disponibilidades->isNotEmpty();
            @endphp

            <div class="col">
                <article class="busqueda-consultor-card busqueda-consultor-card-2">

                    {{-- Encabezado visual --}}
                    <div class="busqueda-consultor-cover"></div>

                    <div class="busqueda-card-top">
                        <div class="busqueda-avatar-wrap">
                            @if($consultor->ruta_foto)
                                <img
                                    class="busqueda-avatar-img"
                                    src="{{ \Illuminate\Support\Facades\Storage::url($consultor->ruta_foto) }}"
                                    alt="Foto de {{ $nombreCompleto }}"
                                >
                            @else
                                <span class="busqueda-avatar-initials">
                                    {{ $iniciales }}
                                </span>
                            @endif
                        </div>

                        <span class="busqueda-profile-status {{ $claseAvance }}">
                            {{ $textoAvance }}
                        </span>
                    </div>

                    {{-- Nombre --}}
                    <div class="text-center mt-2">
                        <h5>{{ $nombreCompleto }}</h5>

                        <div class="busqueda-card-subtitle">
                            Consultor FEPADE
                        </div>
                    </div>

                    {{-- Completitud compacta --}}
                    <div class="busqueda-progress-block">
                        <div class="d-flex justify-content-between align-items-center small mb-1">
                            <span>Perfil</span>

                            <strong>
                                {{ $porcentajePerfil }}%
                            </strong>
                        </div>

                        <div class="busqueda-progress-track">
                            <div
                                class="busqueda-progress-fill {{ $claseAvance }}"
                                style="width: {{ $porcentajePerfil }}%;"
                            ></div>
                        </div>
                    </div>

                    {{-- Indicadores principales --}}
                    <div class="busqueda-card-stats">
                        <div>
                            <strong>{{ $experienciasCount }}</strong>
                            <span>Experiencias</span>
                        </div>

                        <div>
                            <strong>{{ $formacionesCount }}</strong>
                            <span>Formación</span>
                        </div>

                        <div>
                            <strong>{{ $idiomasCount }}</strong>
                            <span>Idiomas</span>
                        </div>
                    </div>

                    {{-- Áreas --}}
                    @if($todasLasAreas->isNotEmpty())
                        <div class="busqueda-card-section">

                            <div class="busqueda-card-section-title">
                                Áreas de especialización
                            </div>

                            <div class="busqueda-chip-row">
                                @foreach($areas as $area)
                                    <span>
                                        {{ $area }}
                                    </span>
                                @endforeach

                                @if($areasRestantes > 0)
                                    <span
                                        class="busqueda-chip-more"
                                        title="{{ $areasRestantes }} área{{ $areasRestantes === 1 ? '' : 's' }} adicional{{ $areasRestantes === 1 ? '' : 'es' }}"
                                    >
                                        +{{ $areasRestantes }}
                                    </span>
                                @endif
                            </div>

                        </div>
                    @endif

                    {{-- Habilidades --}}
                    @if($todasLasHabilidades->isNotEmpty())
                        <div class="busqueda-card-section">

                            <div class="busqueda-card-section-title">
                                Habilidades técnicas
                            </div>

                            <div class="busqueda-chip-row soft">
                                @foreach($habilidadesTecnicas as $habilidad)
                                    <span>
                                        {{ $habilidad }}
                                    </span>
                                @endforeach

                                @if($habilidadesRestantes > 0)
                                    <span
                                        class="busqueda-chip-more"
                                        title="{{ $habilidadesRestantes }} habilidad{{ $habilidadesRestantes === 1 ? '' : 'es' }} adicional{{ $habilidadesRestantes === 1 ? '' : 'es' }}"
                                    >
                                        +{{ $habilidadesRestantes }}
                                    </span>
                                @endif
                            </div>

                        </div>
                    @endif

                    {{-- Información operativa --}}
                    @if($ubicacionTexto || $tieneDisponibilidad || $telefonoPrincipal || $emailPrincipal)
                        <div class="busqueda-meta-grid">

                            @if($ubicacionTexto)
                                <div class="busqueda-meta-grid-item">
                                    <i class="fas fa-location-dot"></i>
                                    <span title="{{ $ubicacionTexto }}">
                                        {{ $ubicacionTexto }}
                                    </span>
                                </div>
                            @endif

                            @if($tieneDisponibilidad)
                                <div class="busqueda-meta-grid-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <span title="Información de disponibilidad">
                                        Información de disponibilidad
                                    </span>
                                </div>
                            @endif

                            @if($telefonoPrincipal)
                                <div class="busqueda-meta-grid-item">
                                    <i class="fas fa-phone"></i>
                                    <span title="{{ $telefonoPrincipal }}">
                                        {{ $telefonoPrincipal }}
                                    </span>
                                </div>
                            @endif

                            @if($emailPrincipal)
                                <div class="busqueda-meta-grid-item">
                                    <i class="fas fa-envelope"></i>
                                    <span title="{{ $emailPrincipal }}">
                                        {{ $emailPrincipal }}
                                    </span>
                                </div>
                            @endif

                        </div>
                    @endif

                    {{-- Sin clasificación profesional --}}
                    @if(
                        $todasLasAreas->isEmpty()
                        && $todasLasHabilidades->isEmpty()
                    )
                        <div class="busqueda-card-empty-professional">
                            <i class="fas fa-layer-group"></i>

                            <span>
                                Perfil profesional sin clasificar
                            </span>
                        </div>
                    @endif

                    {{-- Acciones --}}
                    <div class="row g-2 mt-auto busqueda-card-actions">

                        <div class="col-6">
                            <a
                                href="{{ route('fac.consultores.show', $consultor) }}"
                                class="btn btn-primary w-100"
                            >
                                <i class="fa-solid fa-user me-1"></i>
                                Abrir expediente
                            </a>
                        </div>

                        <div class="col-6">
                            @if(\Illuminate\Support\Facades\Route::has('fac.cv.configurar'))
                                <a
                                    href="{{ route('fac.cv.configurar', $consultor) }}"
                                    class="btn btn-outline-primary w-100"
                                >
                                    <i class="fa-solid fa-file-export me-1"></i>
                                    Exportar CV
                                </a>
                            @else
                                <a
                                    href="#"
                                    class="btn btn-outline-primary disabled w-100"
                                    aria-disabled="true"
                                    title="Ruta pendiente del módulo Exportar CV"
                                >
                                    <i class="fa-solid fa-file-export me-1"></i>
                                    Exportar CV
                                </a>
                            @endif
                        </div>

                    </div>

                </article>
            </div>

        @empty

            <div class="col-12">
                <div class="busqueda-empty-state fepade-card">

                    <div class="busqueda-empty-icon">
                        <i class="fa-solid fa-user-magnifying-glass"></i>
                    </div>

                    <h5>
                        No se encontraron consultores
                    </h5>

                    <p>
                        Prueba reduciendo la cantidad de filtros
                        o realiza una búsqueda más general.
                    </p>

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