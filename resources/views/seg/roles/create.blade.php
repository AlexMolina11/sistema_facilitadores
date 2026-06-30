@extends('layouts.app')

@section('title', 'Nuevo rol | Facilitadores FEPADE')
@section('page-title', 'Nuevo rol')
@section('page-subtitle', 'Creación de perfil de acceso')

@section('content')

<x-ui.page-header title="Nuevo rol" subtitle="Define el nombre del rol y los permisos que tendrá dentro del sistema." />

<form method="POST" action="{{ route('seg.roles.store') }}">
    @csrf

    <x-ui.page-card>
        @include('seg.roles.partials.form')

        <x-ui.form-actions
            :back-url="route('seg.roles.index')"
            back-text="Cancelar"
            submit-text="Guardar"
        />
    </x-ui.page-card>
</form>

@endsection