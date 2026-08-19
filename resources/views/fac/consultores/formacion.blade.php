@extends('layouts.app')

@section('title', 'Trayectoria consultor | Facilitadores FEPADE')
@section('page-title', 'Trayectoria Académica y Profesional')
@section('page-subtitle', 'Registra educación formal, educación continua, acreditaciones, capacitaciones y consultorías realizadas')

@section('content')

<x-ui.page-header
    title="Tayectoria"
    subtitle="Actualiza los atestados que respalden tus conocimientos."
/>

@include('fac.consultores.partials._wizard', ['step' => 4, 'consultor' => $consultor])

@foreach($catalogos['tiposFormacion'] as $tipoFormacion)
    @php
        $formaciones = $formacionesPorTipo->get($tipoFormacion->id_tipo_formacion, collect());
    @endphp
    <div class="perfil-panel mb-4">
        
        <div class="perfil-section-header">
            <div>
                <h4>{{ $tipoFormacion->nombre }}</h4>
                <p class="text-muted mb-0">Registros asociados a {{ strtolower($tipoFormacion->nombre) }}.</p>
            </div>

            <button 
                type="button" 
                class="btn btn-fepade"
                onclick="mostrarFormulario('crear-{{ $tipoFormacion->id_tipo_formacion }}')"
            >
                + Añadir registro
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
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <h5>{{ $formacion->titulo }}</h5>
                            <p class="text-muted mb-0">
                                {{ $tipoAtestado->nombre ?? 'Atestado' }}
                                @if($formacion->horas)
                                    · {{ $formacion->horas }} horas
                                @endif
                            </p>
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
                                action="{{ route('fac.consultores.trayectoria.atestados.destroy', [$consultor, $formacion]) }}"
                                onsubmit="return confirm('¿Deseas eliminar este registro de trayectoria?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-link text-danger" title="Eliminar">
                                    🗑
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($formacion->descripcion)
                        <p class="mt-3 mb-0">{{ $formacion->descripcion }}</p>
                    @endif

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Institución</small>
                            <span>{{ $formacion->institucion ?: 'No registrada' }}</span>
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
                            <small class="text-muted d-block">Entidad acreditadora</small>
                            <span>{{ $formacion->entidad_acreditadora ?: 'No registrada' }}</span>
                        </div>

                        <div class="col-md-4">
                            <small class="text-muted d-block">Cliente / institución</small>
                            <span>{{ $formacion->cliente_institucion ?: 'No registrado' }}</span>
                        </div>

                        <div class="col-md-4">
                            <small class="text-muted d-block">Código acreditación</small>
                            <span>{{ $formacion->codigo_acreditacion ?: 'No registrado' }}</span>
                        </div>

                        <div class="col-md-3">
                            <small class="text-muted d-block">Fecha inicio</small>
                            <span>{{ $formacion->fecha_inicio ? $formacion->fecha_inicio->format('d/m/Y') : 'No registrada' }}</span>
                        </div>

                        <div class="col-md-3">
                            <small class="text-muted d-block">Fecha fin</small>
                            <span>{{ $formacion->fecha_fin ? $formacion->fecha_fin->format('d/m/Y') : 'No registrada' }}</span>
                        </div>

                        <div class="col-md-3">
                            <small class="text-muted d-block">Fecha emisión</small>
                            <span>{{ $formacion->fecha_emision ? $formacion->fecha_emision->format('d/m/Y') : 'No registrada' }}</span>
                        </div>

                        <div class="col-md-3">
                            <small class="text-muted d-block">Fecha vencimiento</small>
                            <span>{{ $formacion->fecha_vencimiento ? $formacion->fecha_vencimiento->format('d/m/Y') : 'No registrada' }}</span>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex gap-3 align-items-center">
                        @if($formacion->url_archivo)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($formacion->url_archivo) }}" target="_blank" class="fw-semibold">
                                Ver documento
                            </a>
                            @if($formacion->nombre_archivo_original)
                                <span class="text-muted small">{{ $formacion->nombre_archivo_original }}</span>
                            @endif
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
                No hay registros para {{ strtolower($tipoFormacion->nombre) }}.
            </div>
        @endforelse
    </div>
@endforeach

@include(
    'fac.consultores.partials._wizard_actions',
    [
        'consultor' =>
            $consultor,

        'anterior' =>
            route(
                'fac.consultores.experiencia.edit',
                $consultor
            ),

        'continuarRoute' =>
            route(
                'fac.consultores.formacion.continuar',
                $consultor
            ),

        'continuarLabel' =>
            'Continuar a Especialización',
    ]
)

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
