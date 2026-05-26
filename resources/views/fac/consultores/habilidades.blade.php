@extends('layouts.app')

@section('title', 'Habilidades | Facilitadores FEPADE')
@section('page-title', 'Editar perfil')
@section('page-subtitle', 'Selecciona tus áreas principales, habilidades técnicas y otros servicios.')

@section('content')
@include('fac.consultores.partials._wizard', ['step' => 5, 'consultor' => $consultor])

<form method="POST" action="{{ route('fac.consultores.habilidades.continuar', $consultor) }}">
    @csrf

    <div class="perfil-panel mb-4">
        @foreach($catalogos['tiposHabilidad'] as $tipoHabilidad)
            @php $items = $catalogos['habilidades']->where('id_tipo_habilidad', $tipoHabilidad->id_tipo_habilidad); @endphp
            <div class="habilidad-panel">
                <h4>{{ $tipoHabilidad->nombre }}</h4>
                <p class="text-muted">Selecciona tus {{ strtolower($tipoHabilidad->nombre) }} principales</p>
                <div class="habilidad-grid">
                    @foreach($items as $habilidad)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="habilidades[]" value="{{ $habilidad->id_habilidad }}" id="hab_{{ $habilidad->id_habilidad }}" {{ $consultor->habilidades->contains('id_habilidad', $habilidad->id_habilidad) ? 'checked' : '' }}>
                            <label class="form-check-label" for="hab_{{ $habilidad->id_habilidad }}">{{ $habilidad->nombre }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="habilidad-panel">
            <h4>Tipos de consultoría</h4>
            <p class="text-muted">Selecciona una o varias áreas en las que el consultor puede apoyar.</p>
            <div class="habilidad-grid">
                @foreach($catalogos['tiposConsultoria'] as $tipo)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="tipos_consultoria[]" value="{{ $tipo->id_tipo_consultoria }}" id="tc_{{ $tipo->id_tipo_consultoria }}" {{ $consultor->tiposConsultoria->contains('id_tipo_consultoria', $tipo->id_tipo_consultoria) ? 'checked' : '' }}>
                        <label class="form-check-label" for="tc_{{ $tipo->id_tipo_consultoria }}">{{ $tipo->nombre }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('fac.consultores.formacion.edit', $consultor) }}" class="btn btn-outline-secondary">Anterior: Títulos Académicos</a>
            <button type="submit" class="btn btn-fepade">Guardar y continuar</button>
        </div>
    </div>
</form>

@endsection
