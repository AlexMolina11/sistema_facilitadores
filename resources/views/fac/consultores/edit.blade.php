@extends('layouts.app')

@section('title', 'Perfil personal | Facilitadores FEPADE')
@section('page-title', 'Editar perfil')
@section('page-subtitle', 'Completa y actualiza tu información personal para continuar con el perfil del consultor.')

@section('content')

<x-ui.page-header
    title="Perfil Personal"
    subtitle="Actualiza los datos personales, residencia, documentos y foto del consultor."
/>

@include('fac.consultores.partials._wizard', ['step' => 1, 'consultor' => $consultor])

<div class="perfil-panel mb-4">
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
