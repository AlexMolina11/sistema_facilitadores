@php
    $consultores = $consultores ?? collect();
    $totalConsultores = $totalConsultores ?? (method_exists($consultores, 'total') ? $consultores->total() : $consultores->count());
@endphp

<section class="fepade-card busqueda-resultados">
    <div class="busqueda-resultados-header d-flex justify-content-between align-items-start gap-3 flex-wrap">
        <div>
            <h5 class="mb-1">Resultados de búsqueda</h5>
            <p class="text-muted mb-0">
                {{ $totalConsultores }} consultor(es) encontrados con los criterios actuales.
            </p>
        </div>

        @if($totalConsultores > 0)
            <span class="badge bg-light text-dark border px-3 py-2">
                <i class="fas fa-users me-1"></i> {{ $totalConsultores }} perfiles
            </span>
        @endif
    </div>

    <div class="row g-3">
        @forelse($consultores as $consultor)
            @php
                $nombreCompleto = $consultor->nombre_completo ?? trim(($consultor->nombres ?? '') . ' ' . ($consultor->apellidos ?? ''));
                $iniciales = collect(explode(' ', $nombreCompleto))->filter()->take(2)->map(fn ($parte) => mb_substr($parte, 0, 1))->implode('');
                $correo = $consultor->emails?->firstWhere('principal', true)?->email ?? $consultor->emails?->first()?->email;
                $telefono = $consultor->telefonos?->first()?->numero_telefono;
                $areas = $consultor->areasEspecializacion ?? collect();
                $idiomas = $consultor->idiomas ?? collect();
                $experiencias = $consultor->experienciasLaborales ?? collect();
                $disponibilidades = $consultor->disponibilidades ?? collect();
                $foto = $consultor->ruta_foto ?? null;
            @endphp

            <div class="col-12">
                <article class="consultor-result-card p-3 p-lg-4">
                    <div class="d-flex gap-3 align-items-start flex-wrap flex-lg-nowrap">
                        @if($foto)
                            <img src="{{ asset('storage/' . $foto) }}" alt="Foto de {{ $nombreCompleto }}" class="consultor-avatar">
                        @else
                            <div class="consultor-avatar">
                                {{ $iniciales ?: 'CF' }}
                            </div>
                        @endif

                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between gap-3 align-items-start flex-wrap">
                                <div>
                                    <h5 class="mb-1">{{ $nombreCompleto ?: 'Consultor sin nombre' }}</h5>

                                    <div class="consultor-result-meta">
                                        @if($correo)
                                            <span><i class="fas fa-envelope me-1"></i>{{ $correo }}</span>
                                        @endif

                                        @if($telefono)
                                            <span><i class="fas fa-phone me-1"></i>{{ $telefono }}</span>
                                        @endif

                                        @if($consultor->sexoCatalogo?->nombre)
                                            <span><i class="fas fa-user me-1"></i>{{ $consultor->sexoCatalogo->nombre }}</span>
                                        @endif

                                        @if($consultor->municipio?->nombre_distrito)
                                            <span><i class="fas fa-location-dot me-1"></i>{{ $consultor->municipio->nombre_distrito }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ route('fac.consultores.show', $consultor) }}" class="btn btn-sm btn-fepade">
                                        <i class="fas fa-eye me-1"></i> Ver perfil
                                    </a>
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-lg-6">
                                    <div class="consultor-result-section-title">Áreas de especialización</div>
                                    @forelse($areas->take(5) as $registroArea)
                                        <span class="consultor-badge me-1 mb-1">
                                            {{ $registroArea->areaEspecializacion?->nombre ?? 'Área no disponible' }}
                                        </span>
                                    @empty
                                        <span class="consultor-badge consultor-badge-muted">Sin áreas registradas</span>
                                    @endforelse

                                    @if($areas->count() > 5)
                                        <span class="consultor-badge consultor-badge-muted">+{{ $areas->count() - 5 }}</span>
                                    @endif
                                </div>

                                <div class="col-lg-6">
                                    <div class="consultor-result-section-title">Habilidades técnicas</div>
                                    @php
                                        $habilidades = $areas
                                            ->flatMap(fn ($registroArea) => $registroArea->habilidades ?? collect())
                                            ->map(fn ($detalle) => $detalle->habilidadTecnica?->nombre)
                                            ->filter()
                                            ->unique()
                                            ->values();
                                    @endphp

                                    @forelse($habilidades->take(5) as $habilidad)
                                        <span class="consultor-badge me-1 mb-1">{{ $habilidad }}</span>
                                    @empty
                                        <span class="consultor-badge consultor-badge-muted">Sin habilidades técnicas</span>
                                    @endforelse

                                    @if($habilidades->count() > 5)
                                        <span class="consultor-badge consultor-badge-muted">+{{ $habilidades->count() - 5 }}</span>
                                    @endif
                                </div>

                                <div class="col-lg-6">
                                    <div class="consultor-result-section-title">Idiomas</div>
                                    @forelse($idiomas->take(4) as $idioma)
                                        <span class="consultor-badge consultor-badge-muted me-1 mb-1">
                                            {{ $idioma->idioma?->nombre ?? 'Idioma' }}
                                            @if($idioma->nivel)
                                                · {{ $idioma->nivel->nombre }}
                                            @endif
                                        </span>
                                    @empty
                                        <span class="consultor-badge consultor-badge-muted">Sin idiomas registrados</span>
                                    @endforelse
                                </div>

                                <div class="col-lg-6">
                                    <div class="consultor-result-section-title">Disponibilidad / experiencia</div>
                                    @forelse($disponibilidades->take(3) as $disponibilidad)
                                        <span class="consultor-badge consultor-badge-muted me-1 mb-1">
                                            {{ $disponibilidad->tipoDisponibilidad?->nombre ?? 'Disponible' }}
                                        </span>
                                    @empty
                                        <span class="consultor-badge consultor-badge-muted me-1 mb-1">Sin disponibilidad</span>
                                    @endforelse

                                    @if($experiencias->count() > 0)
                                        <span class="consultor-badge consultor-badge-muted me-1 mb-1">
                                            {{ $experiencias->count() }} experiencia(s)
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-search fa-2x mb-3 d-block"></i>
                    No se encontraron consultores con los filtros seleccionados.
                </div>
            </div>
        @endforelse
    </div>

    @if(method_exists($consultores, 'links'))
        <div class="mt-4 pagination-wrapper">
            {{ $consultores->links('pagination::bootstrap-5') }}
        </div>
    @endif
</section>
