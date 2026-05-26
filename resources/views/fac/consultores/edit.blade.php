@extends('layouts.app')

@section('title', 'Perfil personal | Facilitadores FEPADE')
@section('page-title', 'Editar perfil')
@section('page-subtitle', 'Completa y actualiza tu información personal para continuar con el perfil del consultor.')

@section('content')
@include('fac.consultores.partials._wizard', ['step' => 1, 'consultor' => $consultor])
@include('fac.consultores.partials._perfil_cards_styles')

<div class="perfil-panel mb-4">
    <div class="perfil-section-header">
        <div>
            <h4>Perfil Personal</h4>
            <p class="text-muted mb-0">Actualiza los datos personales, residencia, documentos y foto del consultor.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('fac.consultores.update', $consultor) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('fac.consultores.partials._form', [
            'consultor' => $consultor,
            'catalogos' => $catalogos
        ])

        <hr class="my-4">

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-fepade">
                Guardar y continuar
            </button>
        </div>
    </form>
</div>
@endsection
