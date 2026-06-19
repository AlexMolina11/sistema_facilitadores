@extends('layouts.app')

@section('title', 'Áreas de especialización | Facilitadores FEPADE')
@section('page-title', 'Editar perfil')
@section('page-subtitle', 'Selecciona áreas de especialización, evidencia y habilidades técnicas aprendidas.')

@section('content')
@include('fac.consultores.partials._wizard', ['step' => 5, 'consultor' => $consultor])

<div class="perfil-panel mb-4">
    <div class="alert alert-info">
        Primero selecciona el área de especialización. Luego vincula un atestado o capacitación FEPADE registrada previamente y marca las habilidades técnicas aprendidas.
    </div>

    <form method="POST" action="{{ route('fac.consultores.habilidades.update', $consultor) }}" class="mb-4">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Área de especialización</label>
                <select name="id_area_especializacion" class="form-select @error('id_area_especializacion') is-invalid @enderror" required>
                    <option value="">Seleccione...</option>
                    @foreach($catalogos['areasEspecializacion'] as $area)
                        <option value="{{ $area->id_area_especializacion }}" {{ old('id_area_especializacion') == $area->id_area_especializacion ? 'selected' : '' }}>
                            {{ $area->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('id_area_especializacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Atestado registrado</label>
                <select name="id_atestado" class="form-select @error('id_atestado') is-invalid @enderror">
                    <option value="">No usar atestado</option>
                    @foreach($consultor->atestados as $atestado)
                        <option value="{{ $atestado->id_atestado }}" {{ old('id_atestado') == $atestado->id_atestado ? 'selected' : '' }}>
                            {{ $atestado->titulo }}
                        </option>
                    @endforeach
                </select>
                @error('id_atestado')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Capacitación FEPADE</label>
                <select name="id_capacitacion_fepade" class="form-select @error('id_capacitacion_fepade') is-invalid @enderror">
                    <option value="">No usar capacitación FEPADE</option>
                    @foreach($consultor->capacitacionesFepade as $capacitacion)
                        <option value="{{ $capacitacion->id_capacitacion_fepade }}" {{ old('id_capacitacion_fepade') == $capacitacion->id_capacitacion_fepade ? 'selected' : '' }}>
                            {{ $capacitacion->nombre_evento }}
                        </option>
                    @endforeach
                </select>
                @error('id_capacitacion_fepade')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mt-4">
            <label class="form-label">¿Qué habilidades técnicas aprendiste?</label>
            <div class="habilidad-grid">
                @foreach($catalogos['habilidadesTecnicas'] as $habilidad)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="habilidades_tecnicas[]" value="{{ $habilidad->id_habilidad_tecnica }}" id="ht_{{ $habilidad->id_habilidad_tecnica }}">
                        <label class="form-check-label" for="ht_{{ $habilidad->id_habilidad_tecnica }}">
                            {{ $habilidad->nombre }}
                            <small class="d-block text-muted">{{ $habilidad->areaEspecializacion?->nombre }}</small>
                        </label>
                    </div>
                @endforeach
            </div>
            @error('habilidades_tecnicas')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
        </div>

        <div class="mt-4 text-end">
            <button class="btn btn-fepade">Agregar área de especialización</button>
        </div>
    </form>

    <hr>

    <h4 class="mb-3">Áreas registradas</h4>

    @forelse($consultor->areasEspecializacion as $registro)
        <div class="card-fepade mb-3">
            <div class="d-flex justify-content-between gap-3 flex-wrap">
                <div>
                    <h5 class="mb-1">{{ $registro->areaEspecializacion?->nombre }}</h5>
                    <p class="text-muted mb-2">
                        Evidencia:
                        @if($registro->atestado)
                            {{ $registro->atestado->titulo }}
                        @elseif($registro->capacitacionFepade)
                            {{ $registro->capacitacionFepade->nombre_evento }}
                        @else
                            Sin evidencia vinculada
                        @endif
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        @forelse($registro->habilidades as $detalle)
                            <span class="badge text-bg-light border">{{ $detalle->habilidadTecnica?->nombre }}</span>
                        @empty
                            <span class="text-muted">Sin habilidades técnicas registradas.</span>
                        @endforelse
                    </div>
                </div>

                <form method="POST" action="{{ route('fac.consultores.habilidades.destroy', [$consultor, $registro]) }}" onsubmit="return confirm('¿Eliminar esta área del perfil?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
            </div>
        </div>
    @empty
        <div class="text-center text-muted py-4">Aún no hay áreas de especialización registradas.</div>
    @endforelse

    <form method="POST" action="{{ route('fac.consultores.habilidades.continuar', $consultor) }}" class="d-flex justify-content-between mt-4">
        @csrf
        <a href="{{ route('fac.consultores.formacion.edit', $consultor) }}" class="btn btn-outline-secondary">Anterior: Títulos Académicos</a>
        <button type="submit" class="btn btn-fepade">Continuar a idiomas</button>
    </form>
</div>
@endsection
