@extends('layouts.app')

@section('title', 'Referencias | Facilitadores FEPADE')
@section('page-title', 'Editar perfil')
@section('page-subtitle', 'Registra referencias personales y profesionales.')

@section('content')
@include('fac.consultores.partials._wizard', ['step' => 7, 'consultor' => $consultor])

<div class="perfil-panel mb-4">
    @foreach($catalogos['tiposReferencia'] as $tipoReferencia)
        @php $items = $referenciasPorTipo->get($tipoReferencia->id_tipo_referencia, collect()); @endphp
        <section class="mb-5">
            <div class="perfil-section-header">
                <div><h4>{{ $tipoReferencia->nombre }}</h4><p class="text-muted mb-0">Máximo 3 referencias para este tipo. Registradas: {{ $items->count() }}/3.</p></div>
                @if($items->count() < 3)
                    <button type="button" class="btn btn-fepade" onclick="mostrarFormularioPerfil('crear-referencia-{{ $tipoReferencia->id_tipo_referencia }}')">+ Añadir referencia</button>
                @endif
            </div>

            <div id="crear-referencia-{{ $tipoReferencia->id_tipo_referencia }}" class="perfil-form-wrapper d-none">
                @include('fac.consultores.partials._referencia_form', ['consultor' => $consultor, 'catalogos' => $catalogos, 'tipoReferencia' => $tipoReferencia, 'referencia' => null])
            </div>

            @forelse($items as $referencia)
                @php $relacion = $catalogos['tiposRelacion']->firstWhere('id_tipo_relacion', $referencia->id_tipo_relacion); @endphp
                <article class="perfil-card-item">
                    <div class="perfil-card-icon">👤</div>
                    <div class="perfil-card-body">
                        <div class="d-flex justify-content-between gap-3">
                            <div>
                                <div class="perfil-card-title">{{ $referencia->nombre }}</div>
                                @if($relacion)<div class="perfil-card-subtitle">Relación: {{ $relacion->nombre }}</div>@endif
                                @if($referencia->cargo || $referencia->empresa)<div class="perfil-card-subtitle">{{ $referencia->cargo }} @if($referencia->empresa) · {{ $referencia->empresa }} @endif</div>@endif
                                <div class="perfil-card-meta">@if($referencia->telefono) {{ $referencia->telefono }} @endif @if($referencia->correo) · {{ $referencia->correo }} @endif</div>
                            </div>
                            <div class="d-flex gap-2"><button type="button" class="btn btn-sm btn-link text-secondary" onclick="mostrarFormularioPerfil('editar-referencia-{{ $referencia->id_referencia }}')">✎</button><form method="POST" action="{{ route('fac.consultores.referencias.destroy', [$consultor, $referencia]) }}" onsubmit="return confirm('¿Deseas eliminar esta referencia?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-link text-danger">🗑</button></form></div>
                        </div>
                        <div id="editar-referencia-{{ $referencia->id_referencia }}" class="perfil-form-wrapper d-none mt-3">
                            @include('fac.consultores.partials._referencia_form', ['consultor' => $consultor, 'catalogos' => $catalogos, 'tipoReferencia' => $tipoReferencia, 'referencia' => $referencia])
                        </div>
                    </div>
                </article>
            @empty
                <div class="text-muted border rounded p-4 mb-3">No hay referencias registradas para este tipo.</div>
            @endforelse
        </section>
    @endforeach
</div>

<form method="POST" action="{{ route('fac.consultores.referencias.continuar', $consultor) }}" class="mb-5">@csrf
    <div class="d-flex justify-content-between">
        <a href="{{ route('fac.consultores.idiomas.edit', $consultor) }}" class="btn btn-outline-secondary">Anterior: Idiomas</a>
        <button type="submit" class="btn btn-fepade">Guardar y continuar</button>
    </div>
</form>
@endsection
@push('scripts')<script>function mostrarFormularioPerfil(id){document.querySelectorAll('.perfil-form-wrapper').forEach(el=>el.classList.add('d-none'));const t=document.getElementById(id);if(t)t.classList.remove('d-none');}function cerrarFormulariosPerfil(){document.querySelectorAll('.perfil-form-wrapper').forEach(el=>el.classList.add('d-none'));}</script>@endpush
