@extends('layouts.app')

@section('title', 'Nuevo permiso | Facilitadores FEPADE')
@section('page-title', 'Nuevo permiso')
@section('page-subtitle', 'Creación de acción autorizable')

@section('content')

<x-ui.page-header
    title="Nuevo permiso"
    subtitle="Registra una nueva acción que podrá asignarse a roles o usuarios específicos."
/>

<form method="POST" action="{{ route('seg.permisos.store') }}">
    @csrf

    <x-ui.page-card>
        @include('seg.permisos.partials.form')

        <x-ui.form-actions
            :back-url="route('seg.permisos.index')"
            back-text="Cancelar"
            submit-text="Guardar permiso"
        />
    </x-ui.page-card>
</form>

@endsection