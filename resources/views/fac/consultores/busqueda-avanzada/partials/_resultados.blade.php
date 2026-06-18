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

                    <div class="d-grid gap-2 mt-auto">
                        <a href="{{ route('fac.consultores.show', $consultor) }}" class="btn btn-primary">
                            <i class="fa-solid fa-user me-1"></i> Ver perfil
                        </a>

                        @if(\Illuminate\Support\Facades\Route::has('fac.cv.preview'))
                            <a href="{{ route('fac.cv.preview', ['consultor' => $consultor->id_consultor]) }}" class="btn btn-outline-primary">
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
                <div class="fepade-card text-center py-5">
                    <h5 class="mb-2">No se encontraron consultores</h5>
                    <p class="text-muted mb-3">Prueba reduciendo la cantidad de filtros o limpia la búsqueda.</p>
                    <a href="{{ route('fac.busqueda.index') }}" class="btn btn-outline-secondary">Limpiar filtros</a>
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
