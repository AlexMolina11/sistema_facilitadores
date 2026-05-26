@extends('layouts.app')

@section('title', 'Formación consultor | Facilitadores FEPADE')
@section('page-title', 'Títulos Académicos y atestados')
@section('page-subtitle', 'Registra formación académica y educación continua')

@section('content')

@include('fac.consultores.partials._wizard', ['step' => 4, 'consultor' => $consultor])

<div class="formacion-panel">
    @foreach($catalogos['tiposFormacion'] as $tipoFormacion)
        @php
            $formaciones = $formacionesPorTipo->get($tipoFormacion->id_tipo_formacion, collect());
        @endphp

        <section class="formacion-section">
            <div class="formacion-section-header">
                <h4>{{ $tipoFormacion->nombre }}</h4>

                <button 
                    type="button" 
                    class="btn btn-fepade"
                    onclick="mostrarFormulario('crear-{{ $tipoFormacion->id_tipo_formacion }}')"
                >
                    + Añadir atestado
                </button>
            </div>

            <div id="crear-{{ $tipoFormacion->id_tipo_formacion }}" class="formacion-form-wrapper d-none">
                @include('fac.consultores.partials._formacion_form', [
                    'consultor' => $consultor,
                    'catalogos' => $catalogos,
                    'tipoFormacion' => $tipoFormacion,
                    'formacion' => null
                ])
            </div>

            @forelse($formaciones as $formacion)
                @php
                    $tipoAtestado = $catalogos['tiposAtestado']->firstWhere('id_tipo_atestado', $formacion->id_tipo_atestado);
                    $nivel = $catalogos['nivelesAcademicos']->firstWhere('id_nivel_academico', $formacion->id_nivel_academico);
                    $pais = $catalogos['paises']->firstWhere('id_pais', $formacion->id_pais);
                @endphp

                <article class="formacion-card">
                    <div class="formacion-icon">
                        📄
                    </div>

                    <div class="formacion-card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5>{{ $formacion->descripcion }}</h5>
                                <p class="text-muted mb-0">{{ $tipoAtestado->nombre ?? 'Atestado' }}</p>
                            </div>

                            <div class="d-flex gap-2">
                                <button 
                                    type="button" 
                                    class="btn btn-sm btn-link text-secondary"
                                    onclick="mostrarFormulario('editar-{{ $formacion->id_atestado }}')"
                                    title="Editar"
                                >
                                    ✎
                                </button>

                                <form 
                                    method="POST" 
                                    action="{{ route('fac.consultores.formacion.atestados.destroy', [$consultor, $formacion]) }}"
                                    onsubmit="return confirm('¿Deseas eliminar este atestado?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-link text-danger" title="Eliminar">
                                        🗑
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Institución</small>
                                <span>{{ $formacion->institucion }}</span>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">Nivel educativo</small>
                                <span>{{ $nivel->nombre ?? 'No registrado' }}</span>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">País</small>
                                <span>{{ $pais->nombre_pais ?? 'No registrado' }}</span>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">Fecha inicio</small>
                                <span>{{ $formacion->fecha_inicio ? $formacion->fecha_inicio->format('Y') : 'No registrada' }}</span>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">Fecha fin</small>
                                <span>{{ $formacion->fecha_fin ? $formacion->fecha_fin->format('Y') : 'No registrada' }}</span>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex gap-3 align-items-center">
                            @if($formacion->url)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($formacion->url) }}" target="_blank" class="fw-semibold">
                                    Ver documento
                                </a>
                            @else
                                <span class="text-muted">Sin documento adjunto</span>
                            @endif
                        </div>

                        <div id="editar-{{ $formacion->id_atestado }}" class="formacion-form-wrapper d-none mt-3">
                            @include('fac.consultores.partials._formacion_form', [
                                'consultor' => $consultor,
                                'catalogos' => $catalogos,
                                'tipoFormacion' => $tipoFormacion,
                                'formacion' => $formacion
                            ])
                        </div>
                    </div>
                </article>
            @empty
                <div class="text-muted border rounded p-4 mb-4">
                    No hay atestados registrados para {{ strtolower($tipoFormacion->nombre) }}.
                </div>
            @endforelse
        </section>
    @endforeach
</div>

<form method="POST" action="{{ route('fac.consultores.formacion.continuar', $consultor) }}" class="mt-4">
    @csrf

    <div class="d-flex justify-content-between">
        <a href="{{ route('fac.consultores.experiencia.edit', $consultor) }}" class="btn btn-outline-secondary">
            Anterior: Experiencia
        </a>

        <button type="submit" class="btn btn-fepade">
            Guardar y continuar
        </button>
    </div>
</form>

<script>
    function cerrarFormularios() {
        document.querySelectorAll('.formacion-form-wrapper').forEach(element => {
            element.classList.add('d-none');
        });
    }

    function mostrarFormulario(id) {
        const element = document.getElementById(id);

        if (!element) {
            return;
        }

        const estabaOculto = element.classList.contains('d-none');

        cerrarFormularios();

        if (estabaOculto) {
            element.classList.remove('d-none');
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
</script>

@endsection