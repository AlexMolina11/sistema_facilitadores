@extends('layouts.app')

@section('title', 'Editar permiso | Facilitadores FEPADE')
@section('page-title', 'Editar permiso')
@section('page-subtitle', 'Actualización de acción autorizable')

@section('content')

<x-ui.page-header
    title="Editar permiso"
    subtitle="Actualiza el código, nombre, módulo y estado del permiso seleccionado."
/>

<form method="POST" action="{{ route('seg.permisos.update', $permiso) }}">
    @csrf
    @method('PUT')

    <x-ui.page-card>
        @include('seg.permisos.partials.form')

        <x-ui.form-actions
            :back-url="route('seg.permisos.index')"
            back-text="Cancelar"
            submit-text="Actualizar permiso"
        />
    </x-ui.page-card>
</form>

@endsection