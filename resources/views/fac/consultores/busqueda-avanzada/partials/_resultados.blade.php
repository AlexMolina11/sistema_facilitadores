@php
    $totalResultados = $totalConsultores ?? ($consultores->total() ?? 0);
@endphp

<section class="busqueda-resultados" data-total-resultados="{{ $totalResultados }}">
    <div class="busqueda-resultados-header fepade-card mb-3">
        <div>
            <span class="busqueda-section-kicker">Resultados</span>
            <h5>Consultores encontrados</h5>
            <p>{{ $totalResultados }} resultado{{ $totalResultados === 1 ? '' : 's' }} según los filtros aplicados.</p>
        </div>

        <div class="busqueda-resultados-total">
            <strong>{{ $totalResultados }}</strong>
            <span>perfil{{ $totalResultados === 1 ? '' : 'es' }}</span>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
        @forelse($consultores as $consultor)
            @php
                $nombreCompleto = $consultor->nombre_completo ?: trim(($consultor->nombres ?? '') . ' ' . ($consultor->apellidos ?? ''));
                $iniciales = strtoupper(mb_substr($consultor->nombres ?? 'C', 0, 1) . mb_substr($consultor->apellidos ?? 'F', 0, 1));

                $telefonoPrincipal = optional($consultor->telefonos->first())->numero_telefono;
                $emailPrincipal = optional($consultor->emails->firstWhere('principal', true))->email ?? optional($consultor->emails->first())->email;

                $avancePerfil = $consultor->avancePerfil();
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

                $areas = $consultor->areasEspecializacion
                    ->pluck('areaEspecializacion.nombre')
                    ->filter()
                    ->unique()
                    ->take(3);

                $habilidadesTecnicas = $consultor->areasEspecializacion
                    ->flatMap(fn ($area) => $area->habilidades)
                    ->pluck('habilidadTecnica.nombre')
                    ->filter()
                    ->unique()
                    ->take(4);

                $experienciasCount = $consultor->experienciasLaborales->count();
                $formacionesCount = $consultor->atestados->count() + $consultor->capacitacionesFepade->count();
                $idiomasCount = $consultor->idiomas->count();
            @endphp

            <div class="col">
                <article class="busqueda-consultor-card busqueda-consultor-card-2">
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
                                <span class="busqueda-avatar-initials">{{ $iniciales }}</span>
                            @endif
                        </div>

                        <span class="busqueda-profile-status {{ $claseAvance }}">
                            {{ $textoAvance }}
                        </span>
                    </div>

                    <div class="text-center mt-2">
                        <h5>{{ $nombreCompleto }}</h5>
                        <div class="busqueda-card-subtitle">Consultor FEPADE</div>
                    </div>

                    <div class="busqueda-progress-block">
                        <div class="d-flex justify-content-between align-items-center small mb-1">
                            <span>Completitud del perfil</span>
                            <strong>{{ $porcentajePerfil }}%</strong>
                        </div>

                        <div class="busqueda-progress-track">
                            <div class="busqueda-progress-fill {{ $claseAvance }}" style="width: {{ $porcentajePerfil }}%;"></div>
                        </div>
                    </div>

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

                    <div class="busqueda-card-section">
                        <div class="busqueda-card-section-title">Áreas de especialización</div>
                        <div class="busqueda-chip-row">
                            @forelse($areas as $area)
                                <span>{{ $area }}</span>
                            @empty
                                <small>Sin áreas registradas</small>
                            @endforelse
                        </div>
                    </div>

                    <div class="busqueda-card-section">
                        <div class="busqueda-card-section-title">Habilidades técnicas</div>
                        <div class="busqueda-chip-row soft">
                            @forelse($habilidadesTecnicas as $habilidad)
                                <span>{{ $habilidad }}</span>
                            @empty
                                <small>Sin habilidades registradas</small>
                            @endforelse
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-auto">
                        <a href="{{ route('fac.consultores.show', $consultor) }}" class="btn btn-primary">
                            <i class="fa-solid fa-user me-1"></i> Abrir expediente
                        </a>

                        @if(\Illuminate\Support\Facades\Route::has('fac.cv.configurar'))
                            <a href="{{ route('fac.cv.configurar', $consultor) }}" class="btn btn-outline-primary">
                                <i class="fa-solid fa-file-export me-1"></i> Exportar CV
                            </a>
                        @else
                            <a href="#" class="btn btn-outline-primary disabled" aria-disabled="true" title="Ruta pendiente del módulo Exportar CV">
                                <i class="fa-solid fa-file-export me-1"></i> Exportar CV
                            </a>
                        @endif
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="busqueda-empty-state fepade-card">
                    <div class="busqueda-empty-icon">
                        <i class="fa-solid fa-user-magnifying-glass"></i>
                    </div>
                    <h5>No se encontraron consultores</h5>
                    <p>Prueba reduciendo la cantidad de filtros o realiza una búsqueda más general.</p>
                    <a href="{{ route('fac.busqueda.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-eraser me-1"></i> Limpiar filtros
                    </a>
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
