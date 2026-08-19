@extends('layouts.app')

@section('title', 'Áreas de especialización | Facilitadores FEPADE')
@section('page-title', 'Editar perfil')
@section('page-subtitle', 'Selecciona áreas de especialización, evidencia y habilidades técnicas aprendidas.')

@section('content')

<x-ui.page-header
    title="Especialización y habilidades"
    subtitle="Selecciona tus áreas de especialización y las habilidades técnicas aprendidas."
/>

@include('fac.consultores.partials._wizard', ['step' => 5, 'consultor' => $consultor])

<div class="perfil-panel mb-4">
    <div class="alert alert-info">
        Selecciona un área de especialización, vincúlala con un atestado o capacitación FEPADE, y marca las habilidades técnicas aprendidas.
    </div>

    <form method="POST" action="{{ route('fac.consultores.habilidades.update', $consultor) }}" class="mb-4">
        @csrf

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Área de especialización</label>
                <select
                    name="id_area_especializacion"
                    id="id_area_especializacion"
                    class="form-select @error('id_area_especializacion') is-invalid @enderror"
                    required
                >
                    <option value="">Seleccione...</option>
                    @foreach($catalogos['areasEspecializacion'] as $area)
                        <option
                            value="{{ $area->id_area_especializacion }}"
                            @selected(old('id_area_especializacion') == $area->id_area_especializacion)
                        >
                            {{ $area->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('id_area_especializacion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Atestado registrado</label>
                <select
                    name="id_atestado"
                    id="id_atestado"
                    class="form-select @error('id_atestado') is-invalid @enderror"
                >
                    <option value="">No usar atestado</option>
                    @foreach($consultor->atestados as $atestado)
                        <option
                            value="{{ $atestado->id_atestado }}"
                            @selected(old('id_atestado') == $atestado->id_atestado)
                        >
                            {{ $atestado->titulo }}
                        </option>
                    @endforeach
                </select>
                @error('id_atestado')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Capacitación FEPADE</label>
                <select
                    name="id_capacitacion_fepade"
                    id="id_capacitacion_fepade"
                    class="form-select @error('id_capacitacion_fepade') is-invalid @enderror"
                >
                    <option value="">No usar capacitación FEPADE</option>
                    @foreach($consultor->capacitacionesFepade as $capacitacion)
                        <option
                            value="{{ $capacitacion->id_capacitacion_fepade }}"
                            @selected(old('id_capacitacion_fepade') == $capacitacion->id_capacitacion_fepade)
                        >
                            {{ $capacitacion->curso_nombre }}
                        </option>
                    @endforeach
                </select>
                @error('id_capacitacion_fepade')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <label class="form-label mb-0">
                    ¿De este curso, diplomado, certificación o atestado qué habilidades técnicas aprendiste?
                </label>
                <span class="badge bg-light text-dark border" id="contadorHabilidades">
                    0 disponibles
                </span>
            </div>

            <div class="habilidad-grid" id="habilidadesTecnicasGrid">
                @foreach($catalogos['habilidadesTecnicas'] as $habilidad)
                    <div
                        class="form-check habilidad-tecnica-item"
                        data-area="{{ $habilidad->id_area_especializacion }}"
                        style="display: none;"
                    >
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="habilidades_tecnicas[]"
                            value="{{ $habilidad->id_habilidad_tecnica }}"
                            id="ht_{{ $habilidad->id_habilidad_tecnica }}"
                            @checked(in_array($habilidad->id_habilidad_tecnica, old('habilidades_tecnicas', [])))
                        >
                        <label class="form-check-label" for="ht_{{ $habilidad->id_habilidad_tecnica }}">
                            {{ $habilidad->nombre }}
                            <small class="d-block text-muted">
                                {{ $habilidad->areaEspecializacion?->nombre }}
                            </small>
                        </label>
                    </div>
                @endforeach

                <div class="text-muted small" id="mensajeSeleccioneArea">
                    Selecciona primero un área de especialización para ver sus habilidades técnicas.
                </div>

                <div class="text-muted small d-none" id="mensajeSinHabilidades">
                    Esta área no tiene habilidades técnicas activas registradas.
                </div>
            </div>

            @error('habilidades_tecnicas')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-4 text-end">
            <button class="btn btn-fepade">
                <i class="fa-solid fa-plus me-1"></i> Agregar área de especialización
            </button>
        </div>
    </form>

    <hr>

    <h4 class="mb-3">Áreas registradas</h4>

    @forelse($consultor->areasEspecializacion as $registro)
        <div class="fepade-card mb-3">
            <div class="d-flex justify-content-between gap-3 flex-wrap">
                <div class="flex-grow-1">
                    <h5 class="mb-1">{{ $registro->areaEspecializacion?->nombre ?? 'Área no disponible' }}</h5>

                    <p class="text-muted mb-2">
                        <strong>Evidencia:</strong>
                        @if($registro->atestado)
                            {{ $registro->atestado->titulo }}
                        @elseif($registro->capacitacionFepade)
                            {{ $registro->capacitacionFepade->curso_nombre }}
                        @else
                            Sin evidencia vinculada
                        @endif
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        @forelse($registro->habilidades->where('activo', true) as $detalle)
                            <span class="badge bg-light text-dark border">
                                {{ $detalle->habilidadTecnica?->nombre ?? 'Habilidad no disponible' }}
                            </span>
                        @empty
                            <span class="text-muted">Sin habilidades técnicas registradas.</span>
                        @endforelse
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ route('fac.consultores.habilidades.destroy', [$consultor, $registro]) }}"
                    onsubmit="return confirm('¿Eliminar esta área del perfil?')"
                >
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">
                        <i class="fa-solid fa-trash me-1"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="fepade-card text-center text-muted py-4">
            Aún no hay áreas de especialización registradas.
        </div>
    @endforelse

    @include(
        'fac.consultores.partials._wizard_actions',
        [
            'consultor' =>
                $consultor,

            'anterior' =>
                route(
                    'fac.consultores.formacion.edit',
                    $consultor
                ),

            'continuarRoute' =>
                route(
                    'fac.consultores.habilidades.continuar',
                    $consultor
                ),

            'continuarLabel' =>
                'Continuar a Idiomas',
        ]
    )
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const areaSelect = document.getElementById('id_area_especializacion');
    const atestadoSelect = document.getElementById('id_atestado');
    const capacitacionSelect = document.getElementById('id_capacitacion_fepade');
    const habilidadItems = document.querySelectorAll('.habilidad-tecnica-item');
    const mensajeSeleccioneArea = document.getElementById('mensajeSeleccioneArea');
    const mensajeSinHabilidades = document.getElementById('mensajeSinHabilidades');
    const contadorHabilidades = document.getElementById('contadorHabilidades');

    function actualizarHabilidades() {
        const areaSeleccionada = areaSelect?.value || '';
        let visibles = 0;

        habilidadItems.forEach(item => {
            const pertenece = item.dataset.area === areaSeleccionada;
            const checkbox = item.querySelector('input[type="checkbox"]');

            item.style.display = pertenece ? 'block' : 'none';

            if (!pertenece && checkbox) {
                checkbox.checked = false;
            }

            if (pertenece) {
                visibles++;
            }
        });

        if (!areaSeleccionada) {
            mensajeSeleccioneArea?.classList.remove('d-none');
            mensajeSinHabilidades?.classList.add('d-none');
        } else {
            mensajeSeleccioneArea?.classList.add('d-none');
            mensajeSinHabilidades?.classList.toggle('d-none', visibles > 0);
        }

        if (contadorHabilidades) {
            contadorHabilidades.textContent = `${visibles} disponible${visibles === 1 ? '' : 's'}`;
        }
    }

    function sincronizarEvidencia(origen) {
        if (!atestadoSelect || !capacitacionSelect) return;

        if (origen === 'atestado' && atestadoSelect.value) {
            capacitacionSelect.value = '';
        }

        if (origen === 'capacitacion' && capacitacionSelect.value) {
            atestadoSelect.value = '';
        }
    }

    areaSelect?.addEventListener('change', actualizarHabilidades);
    atestadoSelect?.addEventListener('change', () => sincronizarEvidencia('atestado'));
    capacitacionSelect?.addEventListener('change', () => sincronizarEvidencia('capacitacion'));

    actualizarHabilidades();
});
</script>
@endpush