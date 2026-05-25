@extends('layouts.app')

@section('title', 'Idiomas | Facilitadores FEPADE')
@section('page-title', 'Editar perfil')
@section('page-subtitle', 'Registra los idiomas que domina el consultor.')

@section('content')
@include('fac.consultores.partials._wizard', ['step' => 6, 'consultor' => $consultor])
@include('fac.consultores.partials._perfil_cards_styles')

<div class="perfil-panel mb-4">
    <div class="perfil-section-header"><div><h4>Idiomas</h4><p class="text-muted mb-0">Registra cada idioma de forma individual.</p></div><button type="button" class="btn btn-fepade" onclick="mostrarFormularioPerfil('crear-idioma')">+ Añadir idioma</button></div>
    <div id="crear-idioma" class="perfil-form-wrapper d-none">@include('fac.consultores.partials._idioma_form', ['consultor' => $consultor, 'catalogos' => $catalogos, 'idiomaConsultor' => null])</div>

    @forelse($consultor->idiomas as $idiomaConsultor)
        @php $idioma = $catalogos['idiomas']->firstWhere('id_idioma', $idiomaConsultor->id_idioma); $nivel = $catalogos['nivelesIdioma']->firstWhere('id_idioma_nivel', $idiomaConsultor->id_idioma_nivel); @endphp
        <article class="perfil-card-item"><div class="perfil-card-icon">🌐</div><div class="perfil-card-body"><div class="d-flex justify-content-between gap-3"><div><div class="perfil-card-title">{{ $idioma->nombre ?? 'Idioma' }}</div><div class="perfil-card-meta">{{ $nivel->nombre ?? 'Nivel no registrado' }}</div>@if($idiomaConsultor->url_certificado)<a href="{{ Storage::url($idiomaConsultor->url_certificado) }}" target="_blank" class="fw-semibold">Ver certificado</a>@endif</div><div class="d-flex gap-2"><button type="button" class="btn btn-sm btn-link text-secondary" onclick="mostrarFormularioPerfil('editar-idioma-{{ $idiomaConsultor->id_consultor_idioma }}')">✎</button><form method="POST" action="{{ route('fac.consultores.idiomas.destroy', [$consultor, $idiomaConsultor]) }}" onsubmit="return confirm('¿Deseas eliminar este idioma?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-link text-danger">🗑</button></form></div></div><div id="editar-idioma-{{ $idiomaConsultor->id_consultor_idioma }}" class="perfil-form-wrapper d-none mt-3">@include('fac.consultores.partials._idioma_form', ['consultor' => $consultor, 'catalogos' => $catalogos, 'idiomaConsultor' => $idiomaConsultor])</div></div></article>
    @empty
        <div class="text-muted border rounded p-4">No hay idiomas registrados.</div>
    @endforelse
</div>

<form method="POST" action="{{ route('fac.consultores.idiomas.continuar', $consultor) }}" class="d-flex justify-content-end mb-5">@csrf<button type="submit" class="btn btn-fepade">Guardar y continuar</button></form>
@endsection
@push('scripts')<script>function mostrarFormularioPerfil(id){document.querySelectorAll('.perfil-form-wrapper').forEach(el=>el.classList.add('d-none'));const t=document.getElementById(id);if(t)t.classList.remove('d-none');}function cerrarFormulariosPerfil(){document.querySelectorAll('.perfil-form-wrapper').forEach(el=>el.classList.add('d-none'));}</script>@endpush
