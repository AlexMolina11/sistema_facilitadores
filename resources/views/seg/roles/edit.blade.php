@extends('layouts.app')

@section('title', 'Editar rol | Facilitadores FEPADE')
@section('page-title', 'Editar rol')
@section('page-subtitle', 'Actualización de perfil de acceso')

@section('content')

<x-ui.page-header title="Editar rol" subtitle="Actualiza los datos del rol y sus permisos asociados." />

<form method="POST" action="{{ route('seg.roles.update', $rol) }}">
    @csrf
    @method('PUT')

    <x-ui.page-card>
        @include('seg.roles.partials.form')

        <x-ui.form-actions
            :back-url="route('seg.roles.index')"
            back-text="Cancelar"
            submit-text="Actualizar"
        />
    </x-ui.page-card>
</form>

@endsection