@extends('layouts.app')

@section('title', 'Experiencia consultor | Facilitadores FEPADE')
@section('page-title', 'Experiencia del consultor')
@section('page-subtitle', 'Experiencia laboral, disponibilidad, habilidades, idiomas y consultorías')

@section('content')

<x-ui.page-header 
    title="Experiencia del consultor"
    subtitle="{{ $consultor->nombre_completo }}"
/>

@include('fac.consultores.partials._wizard', ['step' => 4, 'consultor' => $consultor])

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Revisa los campos marcados.</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="fepade-card formacion-panel mb-4">
    <section class="formacion-section">
        <div class="formacion-section-header">
            <div>
                <h4>Experiencia laboral</h4>
                <p class="mb-0 text-muted">Registra cada experiencia de forma individual.</p>
            </div>

            <button type="button" class="btn btn-fepade" onclick="mostrarFormularioExperiencia('crear-experiencia')">
                + Añadir experiencia
            </button>
        </div>

        <div id="crear-experiencia" class="formacion-form-wrapper experiencia-form-wrapper d-none">
            @include('fac.consultores.partials._experiencia_laboral_form', [
                'consultor' => $consultor,
                'experiencia' => null
            ])
        </div>

        @forelse($consultor->experienciasLaborales as $experiencia)
            <article class="formacion-card">
                <div class="formacion-icon">💼</div>

                <div class="formacion-card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5>{{ $experiencia->cargo ?: 'Cargo no registrado' }}</h5>
                            <p class="text-muted mb-0">{{ $experiencia->empresa ?: 'Empresa no registrada' }}</p>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-link text-secondary" onclick="mostrarFormularioExperiencia('editar-experiencia-{{ $experiencia->id_experiencia }}')" title="Editar">
                                ✎
                            </button>

                            <form method="POST" action="{{ route('fac.consultores.experiencia.laboral.destroy', [$consultor, $experiencia]) }}" onsubmit="return confirm('¿Deseas eliminar esta experiencia laboral?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-link text-danger" title="Eliminar">
                                    🗑
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <small class="text-muted d-block">Desde</small>
                            <span>{{ $experiencia->desde ? $experiencia->desde->format('d/m/Y') : 'No registrado' }}</span>
                        </div>

                        <div class="col-md-3">
                            <small class="text-muted d-block">Hasta</small>
                            <span>{{ $experiencia->trabajo_actual ? 'Actualidad' : ($experiencia->hasta ? $experiencia->hasta->format('d/m/Y') : 'No registrado') }}</span>
                        </div>

                        <div class="col-md-3">
                            <small class="text-muted d-block">Jefe inmediato</small>
                            <span>{{ $experiencia->jefe_nombre ?: 'No registrado' }}</span>
                        </div>

                        <div class="col-md-3">
                            <small class="text-muted d-block">Teléfono jefe</small>
                            <span>{{ $experiencia->jefe_telefono ?: 'No registrado' }}</span>
                        </div>

                        <div class="col-12">
                            <small class="text-muted d-block">Descripción</small>
                            <span>{{ $experiencia->descripcion ?: 'Sin descripción registrada' }}</span>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex gap-3 align-items-center">
                        @if($experiencia->url_evidencia)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($experiencia->url_evidencia) }}" target="_blank" class="fw-semibold">Ver evidencia</a>
                        @else
                            <span class="text-muted">Sin evidencia adjunta</span>
                        @endif
                    </div>

                    <div id="editar-experiencia-{{ $experiencia->id_experiencia }}" class="formacion-form-wrapper experiencia-form-wrapper d-none mt-3">
                        @include('fac.consultores.partials._experiencia_laboral_form', [
                            'consultor' => $consultor,
                            'experiencia' => $experiencia
                        ])
                    </div>
                </div>
            </article>
        @empty
            <div class="text-muted border rounded p-4 mb-4">
                No hay experiencias laborales registradas.
            </div>
        @endforelse
    </section>
</div>

<form method="POST" action="{{ route('fac.consultores.experiencia.competencias.update', $consultor) }}">
    @csrf

    <div class="fepade-card mb-4">
        <div class="contacto-section-header">
            <div>
                <h4>Disponibilidad</h4>
                <p>Selecciona las disponibilidades aplicables al consultor.</p>
            </div>
        </div>

        <div class="row g-3">
            @foreach($catalogos['tiposDisponibilidad'] as $tipo)
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="disponibilidades[]" value="{{ $tipo->id_tipo_disponibilidad }}" id="disp_{{ $tipo->id_tipo_disponibilidad }}" {{ $consultor->disponibilidades->contains('id_tipo_disponibilidad', $tipo->id_tipo_disponibilidad) ? 'checked' : '' }}>
                        <label class="form-check-label" for="disp_{{ $tipo->id_tipo_disponibilidad }}">{{ $tipo->nombre }}</label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="fepade-card mb-4">
        <div class="contacto-section-header">
            <div>
                <h4>Habilidades</h4>
                <p>Selecciona áreas de especialización, habilidades técnicas y habilidades blandas.</p>
            </div>
        </div>

        @foreach($catalogos['tiposHabilidad'] as $tipoHabilidad)
            <h6 class="expediente-subtitle mt-3">{{ $tipoHabilidad->nombre }}</h6>

            <div class="row g-3">
                @foreach($catalogos['habilidades']->where('id_tipo_habilidad', $tipoHabilidad->id_tipo_habilidad) as $habilidad)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="habilidades[]" value="{{ $habilidad->id_habilidad }}" id="hab_{{ $habilidad->id_habilidad }}" {{ $consultor->habilidades->contains('id_habilidad', $habilidad->id_habilidad) ? 'checked' : '' }}>
                            <label class="form-check-label" for="hab_{{ $habilidad->id_habilidad }}">{{ $habilidad->nombre }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <div class="fepade-card mb-4">
        <div class="contacto-section-header">
            <div>
                <h4>Tipos de consultoría</h4>
                <p>Selecciona una o varias áreas en las que el consultor puede apoyar.</p>
            </div>
        </div>

        <div class="row g-3">
            @foreach($catalogos['tiposConsultoria'] as $tipo)
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="tipos_consultoria[]" value="{{ $tipo->id_tipo_consultoria }}" id="tc_{{ $tipo->id_tipo_consultoria }}" {{ $consultor->tiposConsultoria->contains('id_tipo_consultoria', $tipo->id_tipo_consultoria) ? 'checked' : '' }}>
                        <label class="form-check-label" for="tc_{{ $tipo->id_tipo_consultoria }}">{{ $tipo->nombre }}</label>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-fepade">Guardar competencias</button>
        </div>
    </div>
</form>

<div class="fepade-card formacion-panel mb-4">
    <section class="formacion-section">
        <div class="formacion-section-header">
            <div>
                <h4>Idiomas</h4>
                <p class="mb-0 text-muted">Registra cada idioma de forma individual.</p>
            </div>

            <button type="button" class="btn btn-fepade" onclick="mostrarFormularioExperiencia('crear-idioma')">
                + Añadir idioma
            </button>
        </div>

        <div id="crear-idioma" class="formacion-form-wrapper experiencia-form-wrapper d-none">
            @include('fac.consultores.partials._idioma_form', [
                'consultor' => $consultor,
                'catalogos' => $catalogos,
                'idiomaConsultor' => null
            ])
        </div>

        @forelse($consultor->idiomas as $idiomaConsultor)
            @php
                $idioma = $catalogos['idiomas']->firstWhere('id_idioma', $idiomaConsultor->id_idioma);
                $nivel = $catalogos['nivelesIdioma']->firstWhere('id_idioma_nivel', $idiomaConsultor->id_idioma_nivel);
            @endphp

            <article class="formacion-card">
                <div class="formacion-icon">🌐</div>

                <div class="formacion-card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5>{{ $idioma->nombre ?? 'Idioma' }}</h5>
                            <p class="text-muted mb-0">{{ $nivel->nombre ?? 'Nivel no registrado' }}</p>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-link text-secondary" onclick="mostrarFormularioExperiencia('editar-idioma-{{ $idiomaConsultor->id_consultor_idioma }}')" title="Editar">✎</button>

                            <form method="POST" action="{{ route('fac.consultores.experiencia.idiomas.destroy', [$consultor, $idiomaConsultor]) }}" onsubmit="return confirm('¿Deseas eliminar este idioma?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-link text-danger" title="Eliminar">🗑</button>
                            </form>
                        </div>
                    </div>

                    <hr>

                    @if($idiomaConsultor->url_certificado)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($idiomaConsultor->url_certificado) }}" target="_blank" class="fw-semibold">Ver certificado</a>
                    @else
                        <span class="text-muted">Sin certificado adjunto</span>
                    @endif

                    <div id="editar-idioma-{{ $idiomaConsultor->id_consultor_idioma }}" class="formacion-form-wrapper experiencia-form-wrapper d-none mt-3">
                        @include('fac.consultores.partials._idioma_form', [
                            'consultor' => $consultor,
                            'catalogos' => $catalogos,
                            'idiomaConsultor' => $idiomaConsultor
                        ])
                    </div>
                </div>
            </article>
        @empty
            <div class="text-muted border rounded p-4 mb-4">No hay idiomas registrados.</div>
        @endforelse
    </section>
</div>

<div class="fepade-card formacion-panel mb-4">
    @foreach($catalogos['tiposReferencia'] as $tipoReferencia)
        @php
            $referencias = $referenciasPorTipo->get($tipoReferencia->id_tipo_referencia, collect());
        @endphp

        <section class="formacion-section">
            <div class="formacion-section-header">
                <div>
                    <h4>{{ $tipoReferencia->nombre }}</h4>
                    <p class="mb-0 text-muted">Máximo 3 referencias para este tipo.</p>
                </div>

                <button type="button" class="btn btn-fepade" onclick="mostrarFormularioExperiencia('crear-referencia-{{ $tipoReferencia->id_tipo_referencia }}')" {{ $referencias->count() >= 3 ? 'disabled' : '' }}>
                    + Añadir referencia
                </button>
            </div>

            <div id="crear-referencia-{{ $tipoReferencia->id_tipo_referencia }}" class="formacion-form-wrapper experiencia-form-wrapper d-none">
                @include('fac.consultores.partials._referencia_form', [
                    'consultor' => $consultor,
                    'tipoReferencia' => $tipoReferencia,
                    'referencia' => null
                ])
            </div>

            @forelse($referencias as $referencia)
                <article class="formacion-card">
                    <div class="formacion-icon">👤</div>

                    <div class="formacion-card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5>{{ $referencia->nombre }}</h5>
                                <p class="text-muted mb-0">{{ $referencia->cargo ?: 'Cargo no registrado' }} {{ $referencia->empresa ? ' / '.$referencia->empresa : '' }}</p>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-link text-secondary" onclick="mostrarFormularioExperiencia('editar-referencia-{{ $referencia->id_referencia }}')" title="Editar">✎</button>

                                <form method="POST" action="{{ route('fac.consultores.experiencia.referencias.destroy', [$consultor, $referencia]) }}" onsubmit="return confirm('¿Deseas eliminar esta referencia?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger" title="Eliminar">🗑</button>
                                </form>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Teléfono</small>
                                <span>{{ $referencia->telefono ?: 'No registrado' }}</span>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">Correo</small>
                                <span>{{ $referencia->correo ?: 'No registrado' }}</span>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">Empresa</small>
                                <span>{{ $referencia->empresa ?: 'No registrado' }}</span>
                            </div>
                        </div>

                        <div id="editar-referencia-{{ $referencia->id_referencia }}" class="formacion-form-wrapper experiencia-form-wrapper d-none mt-3">
                            @include('fac.consultores.partials._referencia_form', [
                                'consultor' => $consultor,
                                'tipoReferencia' => $tipoReferencia,
                                'referencia' => $referencia
                            ])
                        </div>
                    </div>
                </article>
            @empty
                <div class="text-muted border rounded p-4 mb-4">
                    No hay referencias registradas para {{ strtolower($tipoReferencia->nombre) }}.
                </div>
            @endforelse
        </section>
    @endforeach
</div>

<form method="POST" action="{{ route('fac.consultores.experiencia.continuar', $consultor) }}" class="mt-4">
    @csrf

    <div class="d-flex justify-content-between">
        <a href="{{ route('fac.consultores.formacion.edit', $consultor) }}" class="btn btn-outline-secondary">
            Anterior: Formación
        </a>

        <button type="submit" class="btn btn-fepade">
            Guardar y continuar
        </button>
    </div>
</form>

<script>
    function cerrarFormulariosExperiencia() {
        document.querySelectorAll('.experiencia-form-wrapper').forEach(element => {
            element.classList.add('d-none');
        });
    }

    function mostrarFormularioExperiencia(id) {
        const element = document.getElementById(id);

        if (!element) {
            return;
        }

        const estabaOculto = element.classList.contains('d-none');

        cerrarFormulariosExperiencia();

        if (estabaOculto) {
            element.classList.remove('d-none');
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
</script>

@endsection
